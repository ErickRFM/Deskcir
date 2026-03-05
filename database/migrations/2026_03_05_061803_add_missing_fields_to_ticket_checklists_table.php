<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ticket_checklists', 'errores')) {
            Schema::table('ticket_checklists', function (Blueprint $table) {
                $table->text('errores')->nullable()->after('pruebas_notes');
            });
        }

        if (!Schema::hasColumn('ticket_checklists', 'observaciones')) {
            Schema::table('ticket_checklists', function (Blueprint $table) {
                $table->text('observaciones')->nullable()->after('errores');
            });
        }

        if (!Schema::hasColumn('ticket_checklists', 'status')) {
            Schema::table('ticket_checklists', function (Blueprint $table) {
                $table->enum('status', ['diagnostico', 'reparacion', 'finalizado'])
                    ->nullable()
                    ->after('observaciones');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ticket_checklists', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('ticket_checklists', 'status')) {
                $columnsToDrop[] = 'status';
            }

            if (Schema::hasColumn('ticket_checklists', 'observaciones')) {
                $columnsToDrop[] = 'observaciones';
            }

            if (Schema::hasColumn('ticket_checklists', 'errores')) {
                $columnsToDrop[] = 'errores';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
