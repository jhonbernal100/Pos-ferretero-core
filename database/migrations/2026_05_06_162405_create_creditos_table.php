<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->unsignedBigInteger('tope_credito')->default(0);
            $table->unsignedBigInteger('saldo_usado')->default(0);
            $table->enum('estado', ['activo', 'bloqueado', 'pagado'])->default('activo');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'cliente_id']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};