<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Gemini\Laravel\Facades\Gemini;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class VisionController extends Controller
{
    private function getVisionToken(): string
    {
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

        return $token['access_token'];
    }

    public function analizar(Request $request)
    {
        $request->validate([
            'imagen' => 'required|string',
        ]);

        try {
            $imageB64 = $request->imagen;

            // 1. Obtener token OAuth2 desde cuenta de servicio
            $accessToken = $this->getVisionToken();

            // 2. Cloud Vision REST API con token OAuth2
            $visionResponse = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type'  => 'application/json',
            ])->post(
                'https://vision.googleapis.com/v1/images:annotate',
                [
                    'requests' => [[
                        'image'    => ['content' => $imageB64],
                        'features' => [['type' => 'TEXT_DETECTION', 'maxResults' => 1]],
                    ]],
                ]
            );

            $textoOCR = $visionResponse->json(
                'responses.0.textAnnotations.0.description'
            ) ?? '';

            if (empty($textoOCR)) {
                return response()->json([
                    'success'      => false,
                    'mensaje'      => 'No se detectó texto en la imagen',
                    'debug_vision' => $visionResponse->json(),
                ], 422);
            }

            // 3. Refinar con Gemini 2.5 Flash
            $prompt = "Eres un experto ferretero colombiano.
Analiza este texto extraído de una etiqueta de producto de ferretería:

\"$textoOCR\"

Devuélveme ÚNICAMENTE un JSON válido con esta estructura exacta, sin texto adicional:
{
  \"nombre\": \"nombre del producto corregido\",
  \"marca\": \"marca si se identifica o null\",
  \"categoria\": \"una de estas categorías: Tornillería, Herramientas, Construcción, Eléctrico, Plomería, Pintura, Seguridad, General\",
  \"unidad\": \"unidad de medida: unidad, metro, kilo, litro, bolsa, caja\",
  \"referencia\": \"referencia o código si se identifica o null\"
}";

            $geminiResponse = Gemini::generativeModel(
                model: 'models/gemini-2.5-flash'
            )->generateContent($prompt);

            $jsonTexto = $geminiResponse->text();
            $jsonTexto = preg_replace('/```json|```/', '', $jsonTexto);
            $jsonTexto = trim($jsonTexto);

            $producto = json_decode($jsonTexto, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success'   => false,
                    'mensaje'   => 'Error procesando respuesta de IA',
                    'texto_ocr' => $textoOCR,
                ], 500);
            }

            return response()->json([
                'success'  => true,
                'producto' => $producto,
                'texto_ocr'=> $textoOCR,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ], 500);
        }
    }
}