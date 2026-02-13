<?php

namespace Database\Seeders;

use App\Models\CallFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CallFeeSeeder extends Seeder
{
    public function run(): void
    {
        CallFee::insert([
            ['name' => 'Dalam Kota (0-10 km)', 'min_distance' => 0, 'max_distance' => 10, 'fee' => 50000, 'active' => true],
            ['name' => 'Kota Menengah (11-20 km)', 'min_distance' => 11, 'max_distance' => 20, 'fee' => 100000, 'active' => true],
            ['name' => 'Luar Kota (21-30 km)', 'min_distance' => 21, 'max_distance' => 30, 'fee' => 150000, 'active' => true],
            ['name' => 'Jarak Jauh (>30 km)', 'min_distance' => 31, 'max_distance' => 999, 'fee' => 200000, 'active' => true],
        ]);
    }
}
