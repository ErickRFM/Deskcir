<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rtc_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 20);
            $table->json('payload');
            $table->string('request_mode', 20)->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rtc_signals');
    }
};
