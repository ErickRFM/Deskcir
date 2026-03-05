<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_checklists', function (Blueprint $table) {

            $table->id();

            // 🔗 Relación con ticket
            $table->foreignId('ticket_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // 🔗 Relación con técnico (usuario)
            $table->foreignId('technician_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // ✅ Checklist
            $table->boolean('diagnostico')->default(false);
            $table->boolean('reparacion')->default(false);
            $table->boolean('pruebas')->default(false);

            // 📝 Notas
            $table->text('diagnostico_notes')->nullable();
            $table->text('reparacion_notes')->nullable();
            $table->text('pruebas_notes')->nullable();

            // 📊 Progreso automático
            $table->enum('progress', [
                'pendiente',
                'diagnostico',
                'reparacion',
                'pruebas',
                'finalizado'
            ])->default('pendiente');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checklists');
    }
};