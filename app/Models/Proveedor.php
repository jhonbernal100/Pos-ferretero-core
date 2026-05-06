<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'tenant_id', 'nombre', 'nit', 'contacto',
        'telefono', 'email', 'ciudad', 'notas', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
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
