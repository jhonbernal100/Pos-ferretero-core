<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('tipo_documento');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('tipo_documento', [
                'ticket',
                'factura_electronica',
                'abono_credito',
            ])->default('ticket')->after('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('tipo_documento');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('tipo_documento', [
                'ticket',
                'factura_electronica',
            ])->default('ticket')->after('cliente_id');
        });
    }
};