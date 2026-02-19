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
        Schema::create('appointments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();

        $table->foreignId('user_id')->constrained('users');

        $table->foreignId('technician_id')->constrained('users');

        $table->date('date');
        $table->string('time');

        $table->string('type')->default('visita');

        $table->string('status')->default('pendiente');

        $table->text('notes')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
