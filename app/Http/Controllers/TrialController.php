<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use App\Models\TrialRequest;

class TrialController extends Controller
{
    // Paso 1 — Mostrar formulario con carga de RUT
    public function index()
    {
        return view('trial.paso1');
    }

    // Paso 1 — Procesar foto del RUT con IA
    public function procesarRut(Request $request)
    {
        $request->validate([
            'imagen' => 'required|string',
        ]);

        try {
            // OCR con Cloud Vision
            $keyFile = json_decode(
                file_get_contents(base_path('gcp-vision-key.json')),
                true
            );

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/cloud-vision',
                $keyFile
            );

            $token = $credentials->fetchAuthToken(HttpHandlerFactory::build());

            $visionResponse = Http::withHeaders([
                'Authorization' => "Bearer {$token['access_token']}",
                'Content-Type'  => 'application/json',
            ])->post('https://vision.googleapis.com/v1/images:annotate', [
                'requests' => [[
                    'image'    => ['content' => $request->imagen],
                    'features' => [['type' => 'TEXT_DETECTION', 'maxResults' => 1]],
                ]],
            ]);

            $textoOCR = $visionResponse->json(
                'responses.0.textAnnotations.0.description'
            ) ?? '';

            if (empty($textoOCR)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se pudo leer el RUT. Intenta con mejor iluminación.',
                ], 422);
            }

            // Extraer datos con Gemini
            $prompt = "Eres un experto en documentos tributarios colombianos.
Analiza este texto extraído de un RUT colombiano:

\"$textoOCR\"

Devuélveme ÚNICAMENTE un JSON válido con esta estructura, sin texto adicional:
{
  \"nombre_negocio\": \"razón social o nombre del negocio\",
  \"nit\": \"número de NIT sin dígito de verificación\",
  \"nombre_representante\": \"nombre del representante legal o propietario\",
  \"ciudad\": \"ciudad o municipio\",
  \"direccion\": \"dirección del establecimiento o null\"
}";

            $geminiResponse = Gemini::generativeModel(
                model: 'models/gemini-2.5-flash'
            )->generateContent($prompt);

            $jsonTexto = preg_replace('/```json|```/', '', $geminiResponse->text());
            $datos     = json_decode(trim($jsonTexto), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success'   => false,
                    'mensaje'   => 'Error procesando el RUT',
                    'texto_ocr' => $textoOCR,
                ], 500);
            }

            // Guardar foto del RUT
            $imagen   = base64_decode($request->imagen);
            $nombre   = 'ruts/' . uniqid() . '.jpg';
            Storage::disk('public')->put($nombre, $imagen);

            return response()->json([
                'success'  => true,
                'datos'    => $datos,
                'rut_foto' => $nombre,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    // Paso 2 — Confirmar datos y enviar código de verificación
    public function confirmar(Request $request)
    {
        $request->validate([
            'nombre_negocio'      => 'required|string|max:191',
            'nit'                 => 'nullable|string|max:20',
            'nombre_representante'=> 'required|string|max:191',
            'email'               => 'required|email|unique:trial_requests,email',
            'telefono'            => 'nullable|string|max:20',
            'ciudad'              => 'nullable|string|max:100',
            'rut_foto'            => 'nullable|string',
        ]);

        // Generar código de verificación 6 dígitos
        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $trial = TrialRequest::create([
            'nombre_negocio'       => $request->nombre_negocio,
            'nit'                  => $request->nit,
            'nombre_representante' => $request->nombre_representante,
            'email'                => $request->email,
            'telefono'             => $request->telefono,
            'ciudad'               => $request->ciudad,
            'direccion'            => $request->direccion,
            'rut_foto'             => $request->rut_foto,
            'estado'               => 'pendiente',
            'codigo_verificacion'  => $codigo,
            'codigo_expira_at'     => now()->addMinutes(30),
        ]);

        // Enviar correo con código
        Mail::raw(
            "Hola {$request->nombre_representante},\n\n" .
            "Tu código de verificación para activar el demo de POS Ferretero es:\n\n" .
            "🔐 {$codigo}\n\n" .
            "Este código expira en 30 minutos.\n\n" .
            "Si no solicitaste este acceso ignora este mensaje.\n\n" .
            "Avanzas Digital — Tu éxito es nuestro objetivo",
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Código de verificación — POS Ferretero');
            }
        );

        return response()->json([
            'success'  => true,
            'trial_id' => $trial->id,
            'mensaje'  => 'Código enviado a ' . $request->email,
        ]);
    }

    // Paso 3 — Verificar código y activar trial
    public function verificar(Request $request)
    {
        $request->validate([
            'trial_id' => 'required|integer',
            'codigo'   => 'required|string|size:6',
        ]);

        $trial = TrialRequest::findOrFail($request->trial_id);

        if ($trial->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'mensaje' => 'Esta solicitud ya fue procesada',
            ], 422);
        }

        if ($trial->codigo_verificacion !== $request->codigo) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Código incorrecto',
            ], 422);
        }

        if (now()->isAfter($trial->codigo_expira_at)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'El código expiró. Solicita uno nuevo.',
            ], 422);
        }

        // Crear tenant
        $tenant = Tenant::create([
            'nombre'               => $trial->nombre_negocio,
            'nit'                  => $trial->nit ?? '000000000-0',
            'telefono'             => $trial->telefono,
            'ciudad'               => $trial->ciudad ?? 'Colombia',
            'plan'                 => 'basico',
            'activo'               => true,
            'subscription_status'  => 'trial',
            'trial_ends_at'        => now()->addDays(30),
        ]);

        // Crear usuario dueño
        $password = Str::random(10);
        $user = User::create([
            'name'      => $trial->nombre_representante,
            'email'     => $trial->email,
            'password'  => bcrypt($password),
            'tenant_id' => $tenant->id,
            'rol'       => 'dueno',
            'activo'    => true,
        ]);

        // Actualizar trial
        $trial->update([
            'estado'        => 'activo',
            'verificado_at' => now(),
            'tenant_id'     => $tenant->id,
        ]);

        // Enviar credenciales por correo
        Mail::raw(
            "¡Bienvenido a POS Ferretero!\n\n" .
            "Hola {$trial->nombre_representante},\n\n" .
            "Tu cuenta ha sido activada exitosamente. Tienes 30 días de prueba gratuita.\n\n" .
            "🔗 Accede en: https://pos-ferretero.avanzas.digital/login\n" .
            "📧 Usuario: {$trial->email}\n" .
            "🔐 Contraseña temporal: {$password}\n\n" .
            "Te recomendamos cambiar tu contraseña al ingresar por primera vez.\n\n" .
            "¿Necesitas ayuda? Contáctanos en https://www.avanzas.digital\n\n" .
            "Avanzas Digital — Tu éxito es nuestro objetivo",
            function ($message) use ($trial) {
                $message->to($trial->email)
                        ->subject('¡Bienvenido a POS Ferretero! — Tus credenciales de acceso');
            }
        );

        return response()->json([
            'success' => true,
            'mensaje' => '¡Cuenta activada! Revisa tu correo con las credenciales de acceso.',
        ]);
    }

    // Reenviar código
    public function reenviarCodigo(Request $request)
    {
        $request->validate([
            'trial_id' => 'required|integer',
        ]);

        $trial  = TrialRequest::findOrFail($request->trial_id);
        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $trial->update([
            'codigo_verificacion' => $codigo,
            'codigo_expira_at'    => now()->addMinutes(30),
        ]);

        Mail::raw(
            "Tu nuevo código de verificación es:\n\n🔐 {$codigo}\n\nExpira en 30 minutos.\n\nAvanzas Digital",
            function ($message) use ($trial) {
                $message->to($trial->email)
                        ->subject('Nuevo código de verificación — POS Ferretero');
            }
        );

        return response()->json([
            'success' => true,
            'mensaje' => 'Nuevo código enviado a ' . $trial->email,
        ]);
    }
}
