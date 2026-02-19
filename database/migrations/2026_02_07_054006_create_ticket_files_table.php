<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_files', function (Blueprint $t) {

            $t->id();

            // 👉 Relación con el ticket
            $t->foreignId('ticket_id')
              ->constrained('tickets')
              ->cascadeOnDelete();

            // 👉 Ruta del archivo
            $t->string('path');

            // 👉 Tipo: imagen, pdf, evidencia, etc
            $t->string('type');

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_files');
    }
};