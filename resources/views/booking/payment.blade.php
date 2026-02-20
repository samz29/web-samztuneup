@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Pembayaran Booking</h3>
            <p>Booking Code: <strong>{{ $booking->booking_code }}</strong></p>

            @if($booking->tripay_url)
                <p>Silakan klik tombol di bawah untuk melanjutkan ke halaman pembayaran:</p>
                <a href="{{ $booking->tripay_url }}" class="btn btn-primary" target="_blank">Bayar Sekarang</a>
            @else
                <p>Tidak ada link pembayaran tersedia. Hubungi admin jika Anda merasa ini sebuah kesalahan.</p>
            @endif

            <a href="{{ route('booking.track', ['booking_code' => $booking->booking_code]) }}" class="btn btn-link mt-3">Lihat status booking</a>
        </div>
    </div>
</div>
@endsection
