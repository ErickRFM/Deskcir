<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_signatures', function (Blueprint $t) {

            $t->id();

            // 👉 Relación con el ticket
            $t->foreignId('ticket_id')
              ->constrained('tickets')
              ->cascadeOnDelete();

            // 👉 Firma en base64 o SVG
            $t->text('signature');

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_signatures');
    }
};