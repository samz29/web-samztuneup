<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PartOrder;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Recent bookings for this user
        $recentBookings = Booking::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent part orders: match by email or phone
        $recentPartOrders = PartOrder::where(function ($q) use ($user) {
            $q->where('customer_email', $user->email)
              ->orWhere('customer_phone', $user->phone);
        })->orderBy('created_at', 'desc')->take(5)->get();

        return view('user.dashboard', compact('user', 'recentBookings', 'recentPartOrders'));
    }
}
