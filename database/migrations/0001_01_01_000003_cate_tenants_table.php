<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 191);
            $table->string('nit', 20)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 191)->nullable();
            $table->string('ciudad', 100)->default('Bogotá');
            $table->enum('plan', ['basico', 'profesional', 'premium'])->default('basico');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};