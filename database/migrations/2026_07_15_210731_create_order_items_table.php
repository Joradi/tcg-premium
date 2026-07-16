<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('inventory_id')
                ->nullable()
                ->constrained('inventories')
                ->nullOnDelete();

            $table->string('card_name');
            $table->string('set_name')->nullable();
            $table->string('card_number')->nullable();
            $table->text('image_url')->nullable();

            $table->string('language');
            $table->string('condition');
            $table->string('variant');

            $table->unsignedBigInteger('unit_price')
                ->comment('Precio unitario final con IVA incluido');

            $table->unsignedInteger('quantity');

            $table->unsignedBigInteger('subtotal')
                ->comment('Precio final de la línea con IVA incluido');

            $table->unsignedSmallInteger('tax_rate')
                ->default(1900)
                ->comment('Tasa de IVA en puntos base: 1900 equivale a 19,00%');

            $table->unsignedBigInteger('tax_total')
                ->default(0)
                ->comment('IVA incluido en el subtotal de la línea');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
