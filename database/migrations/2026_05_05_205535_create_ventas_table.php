<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->string('numero_factura', 50)->nullable();
            $table->enum('tipo_documento', ['ticket', 'factura_electronica'])->default('ticket');
            $table->enum('estado', ['borrador', 'completada', 'anulada'])->default('completada');
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('descuento')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'credito'])->default('efectivo');
            $table->unsignedBigInteger('monto_pagado')->default(0);
            $table->unsignedBigInteger('cambio')->default(0);
            $table->boolean('factura_enviada_dian')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};