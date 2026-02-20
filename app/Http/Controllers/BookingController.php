<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\CallFee;
use App\Models\PromoSlider;
use App\Models\Gallery;
use App\Models\Sponsor;
use App\Models\Part;
use App\Services\LocationService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $tripayService;
    protected $locationService;

    public function __construct(TripayService $tripayService, LocationService $locationService)
    {
        $this->tripayService = $tripayService;
        $this->locationService = $locationService;
    }

    public function index()
    {
        $callFees = CallFee::where('active', true)->orderBy('min_distance')->get();
        $promoSliders = PromoSlider::active()->ordered()->get();
        $galleries = Gallery::active()->ordered()->get();
        $sponsors = Sponsor::active()->ordered()->get();
        $parts = Part::active()->ordered()->take(8)->get(); // Limit 8 parts
        return view('booking.index', compact('callFees', 'promoSliders', 'galleries', 'sponsors', 'parts'));
    }

    public function create()
    {
        $paymentChannels = $this->tripayService->getPaymentChannels();
        $callFees = CallFee::where('active', true)->orderBy('min_distance')->get();

        $mapsApiKey = config('services.maps.api_key');
        $baseLat = AppSetting::getValue('workshop_latitude', config('services.samztune.base_latitude', -6.2088));
        $baseLng = AppSetting::getValue('workshop_longitude', config('services.samztune.base_longitude', 106.8456));

        return view('booking.create', compact('paymentChannels', 'callFees', 'mapsApiKey', 'baseLat', 'baseLng'));
    }

    public function calculateFee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string',
            'service_type' => 'required|in:remap_ecu,custom_tune,dyno_tune,full_package',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $fullAddress = "{$request->address}, {$request->district}, {$request->city}, {$request->postal_code}";
            $distanceData = $this->locationService->calculateDistanceFromBase($fullAddress);

            if (!$distanceData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghitung jarak. Pastikan alamat valid.'
                ], 400);
            }

            $basePrice = Booking::getServicePrices()[$request->service_type];
            $callFee = Booking::calculateCallFee($distanceData['distance_km']);
            $totalAmount = $basePrice + $callFee;

            return response()->json([
                'success' => true,
                'data' => [
                    'distance_km' => $distanceData['distance_km'],
                    'base_price' => $basePrice,
                    'call_fee' => $callFee,
                    'total_amount' => $totalAmount,
                    'latitude' => $distanceData['latitude'],
                    'longitude' => $distanceData['longitude'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Calculate fee error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung biaya'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:100',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string',
            'customer_district' => 'required|string',
            'customer_postal_code' => 'required|string',
            'motor_type' => 'required|string|max:50',
            'motor_brand' => 'required|string|max:50',
            'motor_year' => 'required|string|max:4',
            'motor_description' => 'nullable|string',
            'service_type' => 'required|in:remap_ecu,custom_tune,dyno_tune,full_package',
            'base_price' => 'required|numeric|min:0',
            'call_fee' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'payment_method' => 'required|in:tripay,cod,dp',
            'booking_date' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $totalAmount = $request->total_amount;
            $dpAmount = null;

            if ($request->payment_method === 'dp') {
                $dpAmount = $totalAmount * 0.5; // 50% DP
            }

            $booking = Booking::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_address' => $request->customer_address,
                'customer_city' => $request->customer_city,
                'customer_district' => $request->customer_district,
                'customer_postal_code' => $request->customer_postal_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'motor_type' => $request->motor_type,
                'motor_brand' => $request->motor_brand,
                'motor_year' => $request->motor_year,
                'motor_description' => $request->motor_description,
                'service_type' => $request->service_type,
                'base_price' => $request->base_price,
                'call_fee' => $request->call_fee,
                'distance_km' => $request->distance_km ?? 0,
                'payment_method' => $request->payment_method,
                'total_amount' => $totalAmount,
                'dp_amount' => $dpAmount,
                'booking_date' => $request->booking_date,
                'notes' => $request->notes,
                'status' => 'new',
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
            ]);

            // Jika pembayaran Tripay, buat transaksi
            if ($request->payment_method === 'tripay') {
                $paymentData = [
                    'method' => $request->tripay_channel,
                    'merchant_ref' => $booking->booking_code,
                    'amount' => $booking->total_amount,
                    'merchant_code' => config('services.tripay.merchant_code'),
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'order_items' => [
                        [
                            'name' => $booking->service_type_label,
                            'price' => $booking->base_price,
                            'quantity' => 1
                        ],
                        [
                            'name' => 'Biaya Panggilan (' . $booking->distance_km . ' km)',
                            'price' => $booking->call_fee,
                            'quantity' => 1
                        ]
                    ]
                ];

                $tripayResponse = $this->tripayService->createTransaction($paymentData);

                if (isset($tripayResponse['success']) && $tripayResponse['success']) {
                    $booking->update([
                        'tripay_reference' => $tripayResponse['data']['reference'],
                        'tripay_url' => $tripayResponse['data']['checkout_url'],
                    ]);

                    DB::commit();
                    return redirect()->route('booking.payment', $booking->booking_code);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Gagal membuat transaksi Tripay: ' . ($tripayResponse['message'] ?? 'Unknown error'));
                }
            }

            DB::commit();

            if ($request->payment_method === 'cod') {
                return redirect()->route('booking.success', $booking->booking_code)
                    ->with('success', 'Booking berhasil! Teknisi akan datang ke lokasi Anda sesuai jadwal.');
            }

            if ($request->payment_method === 'dp') {
                return redirect()->route('booking.success', $booking->booking_code)
                    ->with('success', 'Booking berhasil! Silakan lakukan pembayaran DP untuk konfirmasi.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking store error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function payment($bookingCode)
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        if ($booking->payment_method !== 'tripay') {
            abort(404);
        }

        return view('booking.payment', compact('booking'));
    }

    public function success($bookingCode)
    {
        $booking = Booking::where('booking_code', $bookingCode)->firstOrFail();

        return view('booking.success', compact('booking'));
    }

    public function track(Request $request)
    {
        if ($request->has('booking_code')) {
            $booking = Booking::where('booking_code', $request->booking_code)->first();

            if ($booking) {
                return view('booking.track', compact('booking'));
            }

            return back()->with('error', 'Booking tidak ditemukan');
        }

        return view('booking.track');
    }

    public function webhook(Request $request)
    {
        $data = $request->all();

        if (!$this->tripayService->verifySignature($data)) {
            return response()->json(['success' => false, 'message' => 'Invalid signature']);
        }

        $booking = Booking::where('booking_code', $data['merchant_ref'])->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found']);
        }

        if ($data['status'] === 'PAID') {
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ]);
        }

        return response()->json(['success' => true]);
    }
}
