<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('descripcion', 191);
            $table->enum('categoria', [
                'Arriendo',
                'Servicios publicos',
                'Nomina',
                'Transporte',
                'Compras de inventario',
                'Mantenimiento',
                'Publicidad',
                'Impuestos',
                'Otros',
            ])->default('Otros');
            $table->unsignedBigInteger('monto');
            $table->date('fecha');
            $table->string('comprobante')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
