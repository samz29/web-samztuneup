@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-success">Booking Berhasil</h3>
            <p>Terima kasih, booking Anda telah diterima.</p>

            <ul class="list-group mb-3">
                <li class="list-group-item">Kode Booking: <strong>{{ $booking->booking_code }}</strong></li>
                <li class="list-group-item">Nama: {{ $booking->customer_name }}</li>
                <li class="list-group-item">Layanan: {{ $booking->service_type_label }}</li>
                <li class="list-group-item">Tanggal: {{ $booking->booking_date->format('d-m-Y H:i') }}</li>
                <li class="list-group-item">Total: Rp {{ number_format($booking->total_amount,0,',','.') }}</li>
            </ul>

            <a href="{{ route('booking.track', ['booking_code' => $booking->booking_code]) }}" class="btn btn-primary">Lihat Status Booking</a>
            <a href="{{ url('/') }}" class="btn btn-link">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection
