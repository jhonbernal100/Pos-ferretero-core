<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'nombre', 'nit', 'telefono',
        'direccion', 'ciudad', 'plan', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function proveedores()
    {
        return $this->hasMany(Proveedor::class);
    }
}