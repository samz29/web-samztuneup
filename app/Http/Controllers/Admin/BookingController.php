<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['user'])->latest()->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mapsApiKey = config('services.maps.api_key');
        $baseLat = AppSetting::getValue('workshop_latitude', config('services.samztune.base_latitude', -6.2088));
        $baseLng = AppSetting::getValue('workshop_longitude', config('services.samztune.base_longitude', 106.8456));

        return view('admin.bookings.create', compact('mapsApiKey', 'baseLat', 'baseLng'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function calculateFee(Request $request)
    {
        $request->validate([
            'distance_km' => 'required|numeric|min:0',
        ]);

        $callFee = Booking::calculateCallFee($request->distance_km);

        return response()->json([
            'fee' => $callFee,
            'formatted_fee' => number_format($callFee, 0, ',', '.')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'customer_district' => 'nullable|string|max:100',
            'customer_postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'motor_type' => 'required|string|max:100',
            'motor_brand' => 'required|string|max:100',
            'motor_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'motor_description' => 'nullable|string',
            'service_type' => 'required|in:remap_ecu,custom_tune,dyno_tune,full_package',
            'base_price' => 'required|numeric|min:0',
            'distance_km' => 'required|numeric|min:0',
            'payment_method' => 'required|in:full,dp',
            'payment_status' => 'required|in:pending,paid,failed,cancelled',
            'dp_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:new,confirmed,on_the_way,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'booking_date' => 'nullable|date',
            'estimated_duration' => 'nullable|string|max:100',
        ]);

        // Calculate call fee based on distance
        $callFee = Booking::calculateCallFee($request->distance_km);

        // Calculate total amount
        $totalAmount = $request->base_price + $callFee;

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
            'call_fee' => $callFee,
            'distance_km' => $request->distance_km,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'dp_amount' => $request->dp_amount ?: 0,
            'total_amount' => $totalAmount,
            'status' => $request->status,
            'notes' => $request->notes,
            'booking_date' => $request->booking_date,
            'estimated_duration' => $request->estimated_duration,
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'notes' => 'nullable|string',
        ]);

        $booking->update($request->only(['status', 'payment_status', 'notes']));

        // Notify customer via Wablas about status/payment update (best-effort)
        try {
            $booking->refresh();
            $wablas = app(\App\Services\WablasService::class);
            $msg = "Halo " . $booking->customer_name . ",\n\n" .
                "Status booking Anda (" . $booking->booking_code . ") telah diperbarui.\n" .
                "Status: " . $booking->getStatusLabelAttribute() . "\n" .
                "Pembayaran: " . $booking->getPaymentStatusLabelAttribute() . "\n\n" .
                "Cek status: " . url(route('booking.track', ['booking_code' => $booking->booking_code], false)) . "\n\n" .
                "Terima kasih,\nSamzTune-UP";

            $wablas->send($booking->customer_phone, $msg, true);
        } catch (\Exception $e) {
            Log::warning('Wablas admin notify failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
