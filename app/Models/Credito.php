<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $fillable = [
        'tenant_id', 'cliente_id', 'tope_credito',
        'saldo_usado', 'estado', 'notas',
    ];

    protected $casts = [
        'tope_credito' => 'integer',
        'saldo_usado'  => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Saldo disponible
    public function saldoDisponible(): int
    {
        return max(0, $this->tope_credito - $this->saldo_usado);
    }

    // Verifica si puede comprar a crédito
    public function puedeComprar(int $monto): bool
    {
        return $this->estado === 'activo' &&
               $this->saldoDisponible() >= $monto;
    }
}