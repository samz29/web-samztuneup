<?php

namespace Database\Seeders;

use App\Models\ServiceRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceRateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'service_name' => 'remap_ecu',
                'description' => 'Remap ECU Standar - Optimasi performa standar untuk harian',
                'base_price' => 750000,
                'unit' => 'service',
                'additional_fee' => 0,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'service_name' => 'custom_tune',
                'description' => 'Custom Tune - Tuning khusus sesuai kebutuhan & modifikasi',
                'base_price' => 1200000,
                'unit' => 'service',
                'additional_fee' => 0,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'service_name' => 'dyno_tune',
                'description' => 'Dyno Tune - Tuning dengan dyno untuk hasil maksimal',
                'base_price' => 950000,
                'unit' => 'service',
                'additional_fee' => 0,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'service_name' => 'full_package',
                'description' => 'Full Package - Remap + Dyno Tune + Garansi',
                'base_price' => 2000000,
                'unit' => 'service',
                'additional_fee' => 0,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($services as $service) {
            ServiceRate::updateOrCreate(
                ['service_name' => $service['service_name']],
                $service
            );
        }
    }
}
