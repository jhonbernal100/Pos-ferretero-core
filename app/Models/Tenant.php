<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tenant extends Model
{
    protected $fillable = [
        'nombre', 'nit', 'telefono',
        'direccion', 'ciudad', 'plan', 'activo',
        'alegra_user', 'alegra_token',
        'alegra_resolucion_id', 'facturacion_electronica',
        'subscription_status', 'trial_ends_at',
        'subscription_ends_at', 'subscription_plan',
        'subscription_price',
    ];

    protected $casts = [
        'activo'                 => 'boolean',
        'facturacion_electronica'=> 'boolean',
        'trial_ends_at'          => 'datetime',
        'subscription_ends_at'   => 'datetime',
        'subscription_price'     => 'integer',
    ];

    // Verifica si el tenant tiene acceso activo
    public function tieneAcceso(): bool
    {
        return match($this->subscription_status) {
            'trial'  => $this->trial_ends_at && $this->trial_ends_at->isFuture(),
            'activa' => $this->subscription_ends_at && $this->subscription_ends_at->isFuture(),
            default  => false,
        };
    }

    // Días restantes del trial o suscripción
    public function diasRestantes(): int
    {
        $fecha = $this->subscription_status === 'trial'
            ? $this->trial_ends_at
            : $this->subscription_ends_at;

        if (!$fecha) return 0;
        return max(0, (int) now()->diffInDays($fecha, false));
    }

    public function productos()   { return $this->hasMany(Producto::class); }
    public function clientes()    { return $this->hasMany(Cliente::class); }
    public function proveedores() { return $this->hasMany(Proveedor::class); }
    public function ventas()      { return $this->hasMany(Venta::class); }
}