<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Gemini\Laravel\Facades\Gemini;

class VisionController extends Controller
{
    public function analizar(Request $request)
    {
        $request->validate([
            'imagen' => 'required|string',
        ]);

        try {
            // 1. Decodificar imagen base64
            $imageData = base64_decode($request->imagen);

            // 2. Enviar a Cloud Vision para OCR
            $imageAnnotator = new ImageAnnotatorClient([
                'credentials' => config('app.google_credentials'),
            ]);

            $image = (new \Google\Cloud\Vision\V1\Image())
                ->setContent($imageData);

            $response = $imageAnnotator->textDetection($image);
            $texts    = $response->getTextAnnotations();
            $imageAnnotator->close();

            $textoOCR = count($texts) > 0
                ? $texts[0]->getDescription()
                : '';

            if (empty($textoOCR)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se detectó texto en la imagen',
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

            $geminiResponse = Gemini::geminiFlash()->generateContent($prompt);
            $jsonTexto      = $geminiResponse->text();

            // Limpiar posibles markdown del response
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
                'success'   => true,
                'producto'  => $producto,
                'texto_ocr' => $textoOCR,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}