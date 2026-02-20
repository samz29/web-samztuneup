<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->text('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_district')->nullable();
            $table->string('customer_postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('motor_type');
            $table->string('motor_brand');
            $table->string('motor_year');
            $table->text('motor_description')->nullable();
            $table->enum('service_type', ['remap_ecu', 'custom_tune', 'dyno_tune', 'full_package']);
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('call_fee', 12, 2)->default(0);
            $table->decimal('distance_km', 8, 2)->default(0);
            $table->enum('payment_method', ['tripay', 'cod', 'dp']);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('dp_amount', 12, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->enum('status', ['new', 'confirmed', 'on_the_way', 'in_progress', 'completed', 'cancelled'])->default('new');
            $table->text('notes')->nullable();
            $table->string('tripay_reference')->nullable();
            $table->string('tripay_url')->nullable();
            $table->dateTime('booking_date');
            $table->string('estimated_duration')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
