<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            // Si no existe priority, la creamos como ENUM
            if (!Schema::hasColumn('tickets', 'priority')) {
                $table->enum('priority', [
                    'baja','media','alta'
                ])->default('media')->after('status');
            }

            // Si no existe assigned_to, la creamos
            if (!Schema::hasColumn('tickets', 'assigned_to')) {
                $table->foreignId('assigned_to')
                      ->nullable()
                      ->constrained('users');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            // Eliminamos foreign y columna assigned_to
            if (Schema::hasColumn('tickets', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }

            // Eliminamos priority
            if (Schema::hasColumn('tickets', 'priority')) {
                $table->dropColumn('priority');
            }

        });
    }
};