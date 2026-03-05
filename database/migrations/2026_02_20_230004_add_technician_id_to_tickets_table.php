<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->unsignedBigInteger('technician_id')
                  ->nullable()
                  ->after('user_id');

            // relación con users
            $table->foreign('technician_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {

            $table->dropForeign(['technician_id']);
            $table->dropColumn('technician_id');
        });
    }
};
