<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (!Schema::hasColumn('product_images', 'disk')) {
                $table->string('disk', 32)->nullable()->after('path');
            }
        });

        Schema::table('ticket_files', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_files', 'disk')) {
                $table->string('disk', 32)->nullable()->after('path');
            }
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_messages', 'disk')) {
                $table->string('disk', 32)->nullable()->after('file');
            }
        });

        Schema::table('checklist_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('checklist_photos', 'disk')) {
                $table->string('disk', 32)->nullable()->after('path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'disk')) {
                $table->dropColumn('disk');
            }
        });

        Schema::table('ticket_files', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_files', 'disk')) {
                $table->dropColumn('disk');
            }
        });

        Schema::table('ticket_messages', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_messages', 'disk')) {
                $table->dropColumn('disk');
            }
        });

        Schema::table('checklist_photos', function (Blueprint $table) {
            if (Schema::hasColumn('checklist_photos', 'disk')) {
                $table->dropColumn('disk');
            }
        });
    }
};
