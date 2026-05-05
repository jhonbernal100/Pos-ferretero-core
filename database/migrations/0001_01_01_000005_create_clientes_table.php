<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre', 191);
            $table->string('tipo_documento', 10)->default('CC');
            $table->string('numero_documento', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('direccion', 191)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'numero_documento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};