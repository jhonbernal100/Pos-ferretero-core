<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_negocio', 191);
            $table->string('nit', 20)->nullable();
            $table->string('nombre_representante', 191);
            $table->string('email', 191)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('direccion', 191)->nullable();
            $table->string('rut_foto')->nullable();
            $table->string('estado', 20)->default('pendiente'); // pendiente, verificado, activo, rechazado
            $table->string('codigo_verificacion', 10)->nullable();
            $table->timestamp('codigo_expira_at')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_requests');
    }
};
