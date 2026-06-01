<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'tenant_id', 'nombre', 'referencia', 'marca',
        'categoria', 'unidad', 'precio_compra', 'precio_venta',
        'stock', 'stock_minimo', 'codigo_barras', 'descripcion',
        'foto', 'activo',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'precio_compra' => 'integer',
        'precio_venta'  => 'integer',
        'stock'         => 'integer',
        'stock_minimo'  => 'integer',
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