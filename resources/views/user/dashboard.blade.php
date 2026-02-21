@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header">Dashboard Pengguna</div>

                <div class="card-body">
                    <h4 class="mb-3">Halo, {{ $user->name }}</h4>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone ?? '—' }}</p>

                    <hr>

                    <div class="list-group">
                        <a href="{{ route('booking.create') }}" class="list-group-item list-group-item-action">Buat Booking Baru</a>
                        <a href="#" class="list-group-item list-group-item-action">Lihat Pesanan</a>
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Halaman Dashboard Umum</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
