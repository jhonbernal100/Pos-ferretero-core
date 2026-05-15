<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('venta_nueva_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->enum('tipo', ['devolucion_simple', 'cambio_producto', 'devolucion_parcial']);
            $table->unsignedBigInteger('monto_devuelto')->default(0);
            $table->unsignedBigInteger('monto_cobrado')->default(0);
            $table->text('motivo')->nullable();
            $table->string('estado', 20)->default('completada');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};