<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialRequest extends Model
{
    protected $fillable = [
        'nombre_negocio', 'nit', 'nombre_representante',
        'email', 'telefono', 'ciudad', 'direccion',
        'rut_foto', 'estado', 'codigo_verificacion',
        'codigo_expira_at', 'verificado_at', 'tenant_id',
    ];

    protected $casts = [
        'codigo_expira_at' => 'datetime',
        'verificado_at'    => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}