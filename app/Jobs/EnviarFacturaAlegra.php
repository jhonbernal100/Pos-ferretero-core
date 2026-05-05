<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Models\Tenant;
use App\Services\AlegraService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarFacturaAlegra implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private Venta $venta
    ) {}

    public function handle(): void
    {
        $tenant  = Tenant::find($this->venta->tenant_id);
        $alegra  = new AlegraService($tenant);

        // Verificar conexión
        if (!$alegra->ping()) {
            Log::error("Alegra: error de autenticación para tenant {$tenant->id}");
            return;
        }

        // Preparar datos de la venta
        $datos = [
            'id'                => $this->venta->id,
            'alegra_cliente_id' => null,
            'detalles'          => $this->venta->detalles->map(fn($d) => [
                'nombre_producto' => $d->nombre_producto,
                'cantidad'        => $d->cantidad,
                'precio_unitario' => $d->precio_unitario,
            ])->toArray(),
        ];

        // Crear factura en Alegra
        $factura = $alegra->crearFactura($datos);

        if ($factura) {
            $this->venta->update([
                'numero_factura'       => $factura['numberTemplate']['fullNumber'] ?? null,
                'factura_enviada_dian' => true,
            ]);

            Log::info("Factura creada en Alegra: {$factura['id']} para venta {$this->venta->id}");
        } else {
            Log::error("Alegra: error creando factura para venta {$this->venta->id}");
            $this->fail();
        }
    }
}