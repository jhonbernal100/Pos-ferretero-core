<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'tenant_id', 'cliente_id', 'numero_factura',
        'tipo_documento', 'estado', 'subtotal',
        'descuento', 'total', 'metodo_pago',
        'monto_pagado', 'cambio', 'factura_enviada_dian', 'notas',
    ];

    protected $casts = [
        'subtotal'              => 'integer',
        'descuento'             => 'integer',
        'total'                 => 'integer',
        'monto_pagado'          => 'integer',
        'cambio'                => 'integer',
        'factura_enviada_dian'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
