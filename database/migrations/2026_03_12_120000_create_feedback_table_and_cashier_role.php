<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30)->default('sugerencia');
            $table->string('subject', 140);
            $table->text('message');
            $table->string('status', 30)->default('nuevo');
            $table->timestamps();
        });

        if (Schema::hasTable('roles')) {
            DB::table('roles')->updateOrInsert(['name' => 'cashier'], ['name' => 'cashier']);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');

        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'cashier')->delete();
        }
    }
};