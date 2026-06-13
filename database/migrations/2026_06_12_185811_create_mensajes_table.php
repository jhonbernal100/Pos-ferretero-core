<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('de_usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto', 191);
            $table->text('contenido');
            $table->enum('tipo', ['plataforma', 'sms', 'ambos'])->default('plataforma');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_at')->nullable();
            $table->enum('estado_sms', ['pendiente', 'enviado', 'fallido'])->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('leido');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};