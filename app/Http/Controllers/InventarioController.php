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
            // 1. Cloud Vision OCR
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