<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre', 191);
            $table->string('referencia', 100)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('categoria', 100)->nullable();
            $table->string('unidad', 50)->default('unidad');
            $table->unsignedBigInteger('precio_compra')->default(0);
            $table->unsignedBigInteger('precio_venta')->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('codigo_barras', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'categoria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};