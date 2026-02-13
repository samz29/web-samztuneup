<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_city',
        'customer_district',
        'customer_postal_code',
        'latitude',
        'longitude',
        'motor_type',
        'motor_brand',
        'motor_year',
        'motor_description',
        'service_type',
        'base_price',
        'call_fee',
        'distance_km',
        'payment_method',
        'total_amount',
        'dp_amount',
        'payment_status',
        'status',
        'notes',
        'tripay_reference',
        'tripay_url',
        'booking_date',
        'estimated_duration',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'call_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'booking_date' => 'datetime',
        'estimated_duration' => 'datetime:Y-m-d H:i:s',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_code = 'STU-' . strtoupper(Str::random(6)) . date('Ymd');
        });
    }

    // Service prices configuration
    public static function getServicePrices()
    {
        $serviceRates = \App\Models\ServiceRate::active()->ordered()->get()->keyBy('service_name');

        $prices = [];
        foreach ($serviceRates as $rate) {
            $prices[$rate->service_name] = $rate->base_price;
        }

        // Ensure all core services have prices (fallback for missing services)
        $coreServices = [
            'remap_ecu' => 750000,
            'custom_tune' => 1200000,
            'dyno_tune' => 950000,
            'full_package' => 2000000,
        ];

        foreach ($coreServices as $service => $defaultPrice) {
            if (!isset($prices[$service])) {
                // Try to find inactive service or create with default price
                $inactiveService = \App\Models\ServiceRate::where('service_name', $service)->first();
                if ($inactiveService) {
                    $prices[$service] = $inactiveService->base_price;
                } else {
                    $prices[$service] = $defaultPrice;
                }
            }
        }

        return $prices;
    }

    public function getServiceTypeLabelAttribute()
    {
        $labels = [
            'remap_ecu' => 'Remap ECU Standar',
            'custom_tune' => 'Custom Tune',
            'dyno_tune' => 'Dyno Tune',
            'full_package' => 'Full Package (Remap + Dyno)',
        ];

        return $labels[$this->service_type] ?? $this->service_type;
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Lunas',
            'failed' => 'Gagal',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'new' => 'Baru',
            'confirmed' => 'Dikonfirmasi',
            'on_the_way' => 'Dalam Perjalanan',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getAmountToPayAttribute()
    {
        if ($this->payment_method === 'dp') {
            return $this->dp_amount;
        }
        return $this->total_amount;
    }

    // Hitung biaya panggilan berdasarkan jarak
    public static function calculateCallFee($distanceKm)
    {
        $callFee = \App\Models\CallFee::where('active', true)
            ->where('min_distance', '<=', $distanceKm)
            ->where('max_distance', '>=', $distanceKm)
            ->first();

        return $callFee ? $callFee->fee : 0;
    }

    // Format alamat lengkap
    public function getFullAddressAttribute()
    {
        return "{$this->customer_address}, {$this->customer_district}, {$this->customer_city} {$this->customer_postal_code}";
    }
}
