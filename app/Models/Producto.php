<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'tenant_id', 'nombre', 'referencia', 'marca',
        'categoria', 'unidad', 'precio_compra', 'precio_venta',
        'stock', 'stock_minimo', 'codigo_barras', 'descripcion', 'activo',
        'foto', 'activo',
    ];

    protected $casts = [
        'precio_compra' => 'integer',
        'precio_venta'  => 'integer',
        'stock'         => 'integer',
        'stock_minimo'  => 'integer',
        'activo'        => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}