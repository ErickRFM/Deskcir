<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ====== PARTE QUE YA TENÍAS (NO SE TOCA) ======
        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('price');
            }

        });

        // ====== LO NUEVO DE TICKETS ======
        Schema::table('tickets', function (Blueprint $table) {

            if (!Schema::hasColumn('tickets', 'priority')) {
                $table->string('priority')->nullable()->after('status');
            }

            if (!Schema::hasColumn('tickets', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('priority');

                $table->foreign('assigned_to')
                      ->references('id')
                      ->on('users');
            }

        });
    }

    public function down(): void
    {
        // ====== REVERSA DE PRODUCTS ======
        Schema::table('products', function (Blueprint $table) {

            if (Schema::hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }

        });

        // ====== REVERSA DE TICKETS ======
        Schema::table('tickets', function (Blueprint $table) {

            if (Schema::hasColumn('tickets', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }

            if (Schema::hasColumn('tickets', 'priority')) {
                $table->dropColumn('priority');
            }

        });
    }
};