<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('nombre_producto', 191);
            $table->integer('cantidad')->default(1);
            $table->unsignedBigInteger('precio_unitario')->default(0);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();

            $table->index('venta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};