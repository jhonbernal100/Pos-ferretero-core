<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'tenant_id', 'venta_id', 'venta_nueva_id',
        'tipo', 'monto_devuelto', 'monto_cobrado',
        'motivo', 'estado', 'usuario_id',
    ];

    protected $casts = [
        'monto_devuelto' => 'integer',
        'monto_cobrado'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function ventaNueva()
    {
        return $this->belongsTo(Venta::class, 'venta_nueva_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}