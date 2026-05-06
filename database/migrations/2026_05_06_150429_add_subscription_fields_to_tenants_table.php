<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('subscription_status', [
                'trial',
                'activa',
                'vencida',
                'suspendida'
            ])->default('trial')->after('facturacion_electronica');

            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
            $table->enum('subscription_plan', [
                'mensual',
                'anual'
            ])->nullable()->after('subscription_ends_at');
            $table->unsignedBigInteger('subscription_price')->default(0)->after('subscription_plan');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_status',
                'trial_ends_at',
                'subscription_ends_at',
                'subscription_plan',
                'subscription_price',
            ]);
        });
    }
};