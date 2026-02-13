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
            $table->string('motor_type');
            $table->string('motor_brand');
            $table->string('motor_year');
            $table->text('motor_description')->nullable();
            $table->enum('service_type', ['remap_ecu', 'custom_tune', 'dyno_tune', 'full_package']);
            $table->enum('payment_method', ['tripay', 'cod', 'dp']);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('dp_amount', 12, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->enum('status', ['new', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('new');
            $table->text('notes')->nullable();
            $table->string('tripay_reference')->nullable();
            $table->string('tripay_url')->nullable();
            $table->dateTime('booking_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
