<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('alegra_user', 191)->nullable()->after('activo');
            $table->string('alegra_token', 191)->nullable()->after('alegra_user');
            $table->string('alegra_resolucion_id', 50)->nullable()->after('alegra_token');
            $table->boolean('facturacion_electronica')->default(false)->after('alegra_resolucion_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'alegra_user',
                'alegra_token',
                'alegra_resolucion_id',
                'facturacion_electronica',
            ]);
        });
    }
};