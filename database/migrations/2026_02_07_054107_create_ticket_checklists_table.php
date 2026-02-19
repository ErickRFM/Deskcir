<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_checklists', function (Blueprint $t) {

            $t->id();

            // 👉 Relación con el ticket
            $t->foreignId('ticket_id')
              ->constrained('tickets')
              ->cascadeOnDelete();

            // 👉 Texto de la tarea
            $t->string('item');

            // 👉 Si ya se completó
            $t->boolean('done')->default(false);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_checklists');
    }
};