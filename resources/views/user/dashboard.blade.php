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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">Booking Terakhir</div>
                                <div class="card-body p-2">
                                    @if(isset($recentBookings) && $recentBookings->count())
                                        <ul class="list-group list-group-flush">
                                            @foreach($recentBookings as $b)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>{{ $b->booking_code }}</strong>
                                                            <div class="small text-muted">{{ $b->booking_date ? $b->booking_date->format('Y-m-d H:i') : $b->created_at->format('Y-m-d') }}</div>
                                                            <div class="small">{{ $b->service_type_label }} — Rp {{ number_format($b->total_amount,0,',','.') }}</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <a href="{{ route('booking.payment', $b->booking_code) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-muted p-3">Belum ada booking.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">Pesanan Spare-part Terakhir</div>
                                <div class="card-body p-2">
                                    @if(isset($recentPartOrders) && $recentPartOrders->count())
                                        <ul class="list-group list-group-flush">
                                            @foreach($recentPartOrders as $o)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>{{ $o->order_code }}</strong>
                                                            <div class="small text-muted">{{ $o->created_at->format('Y-m-d H:i') }}</div>
                                                            <div class="small">Rp {{ number_format($o->total_price,0,',','.') }} (ongkir Rp {{ number_format($o->shipping_cost,0,',','.') }})</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <a href="#" class="btn btn-sm btn-outline-primary">Lihat</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="text-muted p-3">Belum ada pesanan spare-part.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
