<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status')
                ->default('pending')
                ->index();

            $table->string('customer_name');
            $table->string('customer_email')->index();
            $table->string('customer_phone')->nullable();

            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_region');
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_country')->default('Chile');

            $table->unsignedBigInteger('subtotal')
                ->comment('Total de productos con IVA incluido');

            $table->unsignedBigInteger('tax_total')
                ->default(0)
                ->comment('IVA incluido en el total del pedido');

            $table->unsignedBigInteger('shipping_total')
                ->default(0)
                ->comment('Costo final de despacho');

            $table->unsignedBigInteger('total')
                ->comment('Total final cobrado al cliente');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
