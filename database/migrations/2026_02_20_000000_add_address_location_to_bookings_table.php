<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'customer_address')) {
                $table->text('customer_address')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'customer_city')) {
                $table->string('customer_city')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'customer_district')) {
                $table->string('customer_district')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'customer_postal_code')) {
                $table->string('customer_postal_code')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'latitude')) {
                $table->decimal('latitude', 10, 6)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'longitude')) {
                $table->decimal('longitude', 10, 6)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'call_fee')) {
                $table->decimal('call_fee', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('bookings', 'distance_km')) {
                $table->decimal('distance_km', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_address',
                'customer_city',
                'customer_district',
                'customer_postal_code',
                'latitude',
                'longitude',
                'call_fee',
                'distance_km',
            ]);
        });
    }
};
