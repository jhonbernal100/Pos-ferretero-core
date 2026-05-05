<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Tenant;

class AlegraService
{
    private string $baseUrl = 'https://app.alegra.com/api/v1';
    private string $user;
    private string $token;
    private ?string $resolucionId;

    public function __construct(Tenant $tenant)
    {
        // Usa credenciales del tenant si las tiene, si no usa las del .env
        $this->user         = $tenant->alegra_user  ?? env('ALEGRA_USER');
        $this->token        = $tenant->alegra_token ?? env('ALEGRA_TOKEN');
        $this->resolucionId = $tenant->alegra_resolucion_id;
    }

    private function headers(): array
    {
        $credencial = base64_encode("{$this->user}:{$this->token}");

        return [
            'Authorization' => "Basic {$credencial}",
            'Content-Type'  => 'application/json',
        ];
    }

    public function ping(): bool
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/users/self");

        return $response->successful();
    }

    public function crearCliente(array $datos): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/contacts", [
                'name'            => $datos['nombre'],
                'identification'  => $datos['numero_documento'] ?? null,
                'email'           => $datos['email'] ?? null,
                'phonePrimary'    => $datos['telefono'] ?? null,
                'address'         => ['address' => $datos['direccion'] ?? null],
                'type'            => ['client' => true],
            ]);

        return $response->successful() ? $response->json() : null;
    }

    public function crearFactura(array $venta): ?array
    {
        $items = collect($venta['detalles'])->map(fn($d) => [
            'name'        => $d['nombre_producto'],
            'description' => $d['nombre_producto'],
            'quantity'    => $d['cantidad'],
            'price'       => $d['precio_unitario'],
            'tax'         => [],
        ])->toArray();

        $payload = [
            'date'         => now()->format('Y-m-d'),
            'dueDate'      => now()->format('Y-m-d'),
            'observations' => 'Venta POS Ferretero #' . $venta['id'],
            'items'        => $items,
            'paymentMethod' => 'cash',
            'status'       => 'open',
        ];

        // Agregar cliente si existe
        if (!empty($venta['alegra_cliente_id'])) {
            $payload['client'] = ['id' => $venta['alegra_cliente_id']];
        }

        // Agregar resolución DIAN si está configurada
        if ($this->resolucionId) {
            $payload['numberTemplate'] = ['id' => $this->resolucionId];
        }

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/invoices", $payload);

        return $response->successful() ? $response->json() : null;
    }

    public function obtenerFactura(string $id): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/invoices/{$id}");

        return $response->successful() ? $response->json() : null;
    }
}