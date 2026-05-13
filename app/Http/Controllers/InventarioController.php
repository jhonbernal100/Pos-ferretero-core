<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Gemini\Laravel\Facades\Gemini;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class InventarioController extends Controller
{
    public function index()
    {
        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('inventario.index', compact('productos'));
    }

    public function capturar()
    {
        $proveedores = Proveedor::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('inventario.capturar', compact('proveedores'));
    }

    public function analizar(Request $request)
    {
        $request->validate([
            'imagen' => 'required|string',
        ]);

        try {
            $keyFile = json_decode(
                file_get_contents(base_path('gcp-vision-key.json')),
                true
            );

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/cloud-vision',
                $keyFile
            );

            $token = $credentials->fetchAuthToken(
                HttpHandlerFactory::build()
            );

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
                    'mensaje' => 'No se detectó texto en la imagen. Intenta con mejor iluminación.',
                ], 422);
            }

            $prompt = "Eres un experto ferretero colombiano.
Analiza este texto extraído de una etiqueta de producto de ferretería:

\"$textoOCR\"

Devuélveme ÚNICAMENTE un JSON válido con esta estructura, sin texto adicional:
{
  \"nombre\": \"nombre del producto corregido y completo\",
  \"marca\": \"marca si se identifica o null\",
  \"categoria\": \"una de: Tornillería, Herramientas, Construcción, Eléctrico, Plomería, Pintura, Seguridad, General\",
  \"unidad\": \"una de: unidad, metro, kilo, litro, bolsa, caja\",
  \"referencia\": \"referencia o código si se identifica o null\"
}";

            $geminiResponse = Gemini::generativeModel(
                model: 'models/gemini-2.5-flash'
            )->generateContent($prompt);

            $jsonTexto = preg_replace('/```json|```/', '', $geminiResponse->text());
            $jsonTexto = trim($jsonTexto);
            $producto  = json_decode($jsonTexto, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success'   => false,
                    'mensaje'   => 'Error procesando respuesta de IA',
                    'texto_ocr' => $textoOCR,
                ], 500);
            }

            $existente = Producto::where('tenant_id', session('tenant_id'))
                ->where(function ($q) use ($producto) {
                    $q->where('nombre', 'like', '%' . $producto['nombre'] . '%')
                      ->orWhere('referencia', $producto['referencia'] ?? '');
                })->first();

            return response()->json([
                'success'   => true,
                'producto'  => $producto,
                'texto_ocr' => $textoOCR,
                'existente' => $existente ? [
                    'id'            => $existente->id,
                    'nombre'        => $existente->nombre,
                    'stock'         => $existente->stock,
                    'precio_compra' => $existente->precio_compra,
                    'precio_venta'  => $existente->precio_venta,
                ] : null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    public function guardar(Request $request)
{
    $request->validate([
        'nombre'        => 'required|string|max:191',
        'precio_compra' => 'required|integer|min:0',
        'precio_venta'  => 'required|integer|min:0',
        'stock'         => 'required|integer|min:0',
    ]);

    // Guardar foto en storage
    $foto = null;
    if ($request->foto_base64) {
        $imagen = base64_decode($request->foto_base64);
        $nombre = 'productos/' . uniqid() . '.jpg';
        \Storage::disk('public')->put($nombre, $imagen);
        $foto = $nombre;
    }

    $producto = Producto::updateOrCreate(
        [
            'tenant_id'  => session('tenant_id'),
            'referencia' => $request->referencia,
        ],
        [
            'tenant_id'     => session('tenant_id'),
            'nombre'        => $request->nombre,
            'marca'         => $request->marca,
            'categoria'     => $request->categoria,
            'unidad'        => $request->unidad ?? 'unidad',
            'referencia'    => $request->referencia,
            'precio_compra' => $request->precio_compra,
            'precio_venta'  => $request->precio_venta,
            'stock'         => $request->stock,
            'stock_minimo'  => $request->stock_minimo ?? 5,
            'foto'          => $foto,
            'activo'        => true,
        ]
    );

    return response()->json([
        'success'  => true,
        'producto' => $producto,
        'mensaje'  => $producto->wasRecentlyCreated
            ? 'Producto agregado al inventario'
            : 'Inventario actualizado',
    ]);
}

    public function actualizar(Request $request, Producto $producto)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $producto->increment('stock', $request->stock);

        return response()->json([
            'success' => true,
            'mensaje' => "Stock actualizado. Total: {$producto->fresh()->stock} unidades",
        ]);
    }

    public function crear()
    {
        $proveedores = Proveedor::where('activo', true)
            ->orderBy('nombre')
            ->get();
    
        return view('inventario.crear-manual', compact('proveedores'));
    }
    
    public function editar(Producto $producto)
    {
        $proveedores = Proveedor::where('activo', true)
            ->orderBy('nombre')
            ->get();
    
        return view('inventario.editar', compact('producto', 'proveedores'));
    }
      
    public function actualizar_producto(Request $request, Producto $producto)
{
    $request->validate([
        'nombre'        => 'required|string|max:191',
        'precio_compra' => 'required|integer|min:0',
        'precio_venta'  => 'required|integer|min:0',
        'stock'         => 'required|integer|min:0',
    ]);

    // Guardar foto si viene nueva
    $foto = $producto->foto;
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto')->store('productos', 'public');
    } elseif ($request->foto_base64) {
        $imagen = base64_decode($request->foto_base64);
        $nombre = 'productos/' . uniqid() . '.jpg';
        \Storage::disk('public')->put($nombre, $imagen);
        $foto = $nombre;
    }

    $producto->update([
        'nombre'        => $request->nombre,
        'marca'         => $request->marca,
        'categoria'     => $request->categoria,
        'unidad'        => $request->unidad ?? 'unidad',
        'referencia'    => $request->referencia,
        'precio_compra' => $request->precio_compra,
        'precio_venta'  => $request->precio_venta,
        'stock'         => $request->stock,
        'stock_minimo'  => $request->stock_minimo ?? 5,
        'descripcion'   => $request->descripcion,
        'foto'          => $foto,
        'activo'        => true,
    ]);

    return response()->json([
        'success' => true,
        'mensaje' => 'Producto actualizado correctamente',
    ]);
}


}