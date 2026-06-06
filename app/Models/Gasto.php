<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'tenant_id', 'usuario_id', 'descripcion',
        'categoria', 'monto', 'fecha', 'comprobante', 'notas',
    ];

    protected $casts = [
        'monto' => 'integer',
        'fecha' => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}