<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TABLA PRINCIPAL DE ÓRDENES
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();

            $table->string('payment_method');
            $table->string('status')->default('pendiente');

            // Envío
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('phone');

            $table->decimal('total', 10, 2);

            $table->timestamps();
        });

        // TABLA DE PRODUCTOS DE CADA ORDEN
        Schema::create('order_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('qty');

            $table->decimal('price',10,2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};