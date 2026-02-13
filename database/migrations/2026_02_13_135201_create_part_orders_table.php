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
        Schema::create('part_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('part_id')->constrained('parts')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            // Customer info
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // Shipping info
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_postal_code');
            $table->string('shipping_courier')->nullable(); // e.g., "jne", "tiki"
            $table->string('shipping_service')->nullable(); // e.g., "REG", "YES"
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->json('shipping_details')->nullable(); // Store full shipping response

            // Payment info
            $table->string('payment_method')->default('tripay'); // tripay or midtrans
            $table->string('payment_reference')->nullable();
            $table->json('payment_details')->nullable();
            $table->enum('status', ['pending', 'paid', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_orders');
    }
};
