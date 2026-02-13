<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\PartOrder;
use App\Services\BiteshipService;
use App\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PartController extends Controller
{
    protected $biteshipService;
    protected $tripayService;

    public function __construct(BiteshipService $biteshipService, TripayService $tripayService)
    {
        $this->biteshipService = $biteshipService;
        $this->tripayService = $tripayService;
    }

    /**
     * Show part detail
     */
    public function show(Part $part)
    {
        if (!$part->is_active) {
            abort(404);
        }

        return view('parts.show', compact('part'));
    }

    /**
     * Show checkout form
     */
    public function checkout(Request $request, Part $part)
    {
        if (!$part->is_active) {
            abort(404);
        }

        $quantity = $request->get('quantity', 1);
        $quantity = max(1, min(10, $quantity)); // Min 1, max 10

        return view('parts.checkout', compact('part', 'quantity'));
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShipping(Request $request)
    {
        // Temporarily hold parts sales due to shipping calculation issues
        return response()->json([
            'success' => false,
            'message' => 'Penjualan sparepart sementara dihentikan untuk perbaikan sistem. Silakan hubungi workshop untuk informasi lebih lanjut.'
        ]);

        // Handle both JSON and form data
        $data = $request->isJson() ? $request->json()->all() : $request->all();

        $validator = Validator::make($data, [
            'postal_code' => 'required|string|size:5',
            'city' => 'required|string',
            'address' => 'required|string|min:10',
            'part_name' => 'required|string',
            'part_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengiriman tidak lengkap',
                'errors' => $validator->errors()
            ]);
        }

        // Origin (workshop location) - using mixed data (postal code + coordinates)
        $origin = [
            'postal_code' => config('services.samztune.postal_code'),
            'coordinate' => [
                'latitude' => config('services.samztune.base_latitude'),
                'longitude' => config('services.samztune.base_longitude'),
            ],
            'address' => config('services.samztune.address'),
            'city' => config('services.samztune.city'),
            'contact_name' => 'SamzTune-Up',
            'contact_phone' => '081234567890',
        ];

        // Destination - using postal code + optional coordinates
        $destination = [
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
            'city' => $data['city'],
        ];

        // Add coordinates if available (for more accurate calculation)
        if (isset($data['latitude']) && isset($data['longitude'])) {
            $destination['coordinate'] = [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ];
        }

        // Items (simplified - assuming single item)
        $items = [
            [
                'name' => $data['part_name'],
                'description' => $data['part_description'] ?? '',
                'price' => $data['part_price'],
                'length' => 30, // cm
                'width' => 20,  // cm
                'height' => 10, // cm
                'weight' => 1000, // grams
                'quantity' => $data['quantity'],
            ]
        ];

        try {
            $rates = $this->biteshipService->getShippingRates($origin, $destination, $items);

            if ($rates && isset($rates['pricing']) && is_array($rates['pricing'])) {
                // Filter and format rates for frontend
                $formattedRates = array_map(function($rate) {
                    return [
                        'courier_name' => $rate['courier_name'] ?? 'Unknown',
                        'service_name' => $rate['service_name'] ?? 'Regular',
                        'price' => $rate['price'] ?? 0,
                        'estimated_delivery' => $rate['estimated_delivery'] ?? '1-2 hari',
                        'courier_code' => $rate['courier_code'] ?? '',
                        'service_code' => $rate['service_code'] ?? '',
                    ];
                }, $rates['pricing']);

                return response()->json([
                    'success' => true,
                    'rates' => $formattedRates
                ]);
            }

            // Check if it's a Biteship API subscription issue
            if ($rates && isset($rates['error']) && $rates['error'] && isset($rates['error_code']) && $rates['error_code'] == 40001001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan perhitungan ongkos kirim sedang tidak tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.'
                ]);
            }

            // Log the actual API response for debugging
            Log::warning('Biteship API returned invalid response', [
                'response' => $rates,
                'origin' => $origin,
                'destination' => $destination,
                'items' => $items
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghitung ongkos kirim untuk alamat tersebut. Silakan periksa alamat pengiriman atau coba lagi nanti.'
            ]);
        } catch (\Exception $e) {
            Log::error('Shipping calculation error: ' . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi dalam beberapa saat.'
            ]);
        }
    }

    /**
     * Process order
     */
    public function processOrder(Request $request, Part $part)
    {
        // Temporarily hold parts sales due to shipping calculation issues
        return back()->withErrors(['error' => 'Penjualan sparepart sementara dihentikan untuk perbaikan sistem. Silakan hubungi workshop untuk informasi lebih lanjut.']);

        if (!$part->is_active) {
            return back()->withErrors(['error' => 'Part tidak tersedia']);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|min:10',
            'shipping_city' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|size:5',
            'shipping_courier' => 'required|string',
            'shipping_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'payment_method' => 'required|in:tripay,midtrans',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quantity = $request->quantity;
        $unitPrice = $part->price ?? 0;
        $totalPrice = $unitPrice * $quantity;
        $shippingCost = $request->shipping_cost;
        $totalAmount = $totalPrice + $shippingCost;

        // Create order
        $order = PartOrder::create([
            'part_id' => $part->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_courier' => $request->shipping_courier,
            'shipping_service' => $request->shipping_service,
            'shipping_cost' => $shippingCost,
            'payment_method' => $request->payment_method,
        ]);

        // Create payment
        if ($request->payment_method === 'tripay') {
            $paymentData = [
                'method' => 'QRIS', // Default to QRIS
                'merchant_ref' => $order->order_code,
                'amount' => $totalAmount,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'order_items' => [
                    [
                        'sku' => 'PART-' . $part->id,
                        'name' => $part->name,
                        'price' => $unitPrice,
                        'quantity' => $quantity,
                    ]
                ],
                'callback_url' => route('part.payment.callback'),
                'return_url' => route('part.payment.success', $order->order_code),
                'expired_time' => (time() + (24 * 60 * 60)), // 24 hours
            ];

            $payment = $this->tripayService->createPayment($paymentData);

            if ($payment) {
                $order->update([
                    'payment_reference' => $payment['reference'],
                    'payment_details' => $payment,
                ]);

                return redirect($payment['checkout_url']);
            }
        }

        return back()->withErrors(['error' => 'Gagal membuat pembayaran']);
    }

    /**
     * Payment success page
     */
    public function paymentSuccess($orderCode)
    {
        $order = PartOrder::where('order_code', $orderCode)->firstOrFail();
        return view('parts.payment-success', compact('order'));
    }

    /**
     * Payment callback (webhook)
     */
    public function paymentCallback(Request $request)
    {
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, config('services.tripay.private_key'));

        if ($request->header('X-Callback-Signature') !== $signature) {
            return response('Invalid signature', 403);
        }

        $data = json_decode($json, true);

        $order = PartOrder::where('payment_reference', $data['reference'])->first();

        if ($order) {
            if ($data['status'] === 'PAID') {
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Create shipping order
                $this->createShippingOrder($order);
            }
        }

        return response('OK', 200);
    }

    /**
     * Test Biteship API connection
     */
    public function testBiteship()
    {
        $result = $this->biteshipService->testConnection();

        return response()->json($result);
    }
    private function createShippingOrder(PartOrder $order)
    {
        $shippingData = [
            'shipper_contact_name' => 'SamzTune-Up',
            'shipper_contact_phone' => '081234567890',
            'shipper_contact_email' => 'admin@samztune-up.com',
            'shipper_organization' => 'SamzTune-Up',
            'origin_contact_name' => 'SamzTune-Up',
            'origin_contact_phone' => '081234567890',
            'origin_address' => config('services.samztune.address'),
            'origin_note' => 'Workshop SamzTune-Up',
            'origin_postal_code' => config('services.samztune.postal_code'),
            'origin_coordinate' => [
                'latitude' => config('services.samztune.base_latitude'),
                'longitude' => config('services.samztune.base_longitude'),
            ],
            'destination_contact_name' => $order->customer_name,
            'destination_contact_phone' => $order->customer_phone,
            'destination_contact_email' => $order->customer_email,
            'destination_address' => $order->shipping_address,
            'destination_postal_code' => $order->shipping_postal_code,
            'destination_note' => '',
            'destination_coordinate' => [
                'latitude' => null, // Should be calculated from address
                'longitude' => null,
            ],
            'courier_company' => $order->shipping_courier,
            'courier_type' => $order->shipping_service,
            'delivery_type' => 'now',
            'order_note' => 'Pesanan Spare Part - Order #' . $order->order_code,
            'items' => [
                [
                    'name' => $order->part->name,
                    'description' => $order->part->description ?? '',
                    'value' => $order->unit_price,
                    'length' => 30,
                    'width' => 20,
                    'height' => 10,
                    'weight' => 1000,
                    'quantity' => $order->quantity,
                ]
            ],
        ];

        $shipping = $this->biteshipService->createShippingOrder($shippingData);

        if ($shipping) {
            $order->update([
                'shipping_details' => $shipping,
                'status' => 'shipped',
            ]);
        }
    }
}
