<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = [
        'tenant_id', 'de_usuario_id', 'asunto',
        'contenido', 'tipo', 'leido', 'leido_at',
        'estado_sms',
    ];

    protected $casts = [
        'leido'    => 'boolean',
        'leido_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function deUsuario()
    {
        return $this->belongsTo(User::class, 'de_usuario_id');
    }
}