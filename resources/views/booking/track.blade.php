@extends('layouts.app')

@section('title', 'Track Your Booking - SamzTune-Up')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-search fa-2x me-3"></i>
                        <div>
                            <h3 class="mb-0">Track Your Booking</h3>
                            <small class="d-block">Enter your booking code to check status</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(isset($booking))
                        <!-- Booking Details -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <h5 class="alert-heading mb-3">
                                        <i class="fas fa-check-circle me-2"></i>Booking Found!
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Booking Code:</strong> {{ $booking->booking_code }}</p>
                                            <p class="mb-2"><strong>Customer:</strong> {{ $booking->customer_name }}</p>
                                            <p class="mb-2"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                                            <p class="mb-2"><strong>Email:</strong> {{ $booking->customer_email }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Service Type:</strong>
                                                @switch($booking->service_type)
                                                    @case('remap_ecu')
                                                        ECU Remap
                                                        @break
                                                    @case('custom_tune')
                                                        Custom Tune
                                                        @break
                                                    @case('dyno_tune')
                                                        Dyno Tune
                                                        @break
                                                    @case('full_package')
                                                        Full Package
                                                        @break
                                                    @default
                                                        {{ $booking->service_type }}
                                                @endswitch
                                            </p>
                                            <p class="mb-2"><strong>Status:</strong>
                                                @if($booking->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @elseif($booking->status == 'completed')
                                                    <span class="badge bg-primary">Completed</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->status }}</span>
                                                @endif
                                            </p>
                                            <p class="mb-2"><strong>Payment Status:</strong>
                                                @if($booking->payment_status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($booking->payment_status == 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->payment_status }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if($booking->notes)
                                        <div class="mt-3">
                                            <strong>Notes:</strong>
                                            <p class="mb-0">{{ $booking->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="{{ route('booking.track') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Track Another Booking
                            </a>
                        </div>
                    @else
                        <!-- Booking Code Form -->
                        <form action="{{ route('booking.track') }}" method="GET">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="mb-4">
                                        <label for="booking_code" class="form-label fw-bold">Enter Your Booking Code</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                            <input type="text" class="form-control" id="booking_code" name="booking_code"
                                                   placeholder="e.g., BK-20240212-001"
                                                   value="{{ old('booking_code') }}" required
                                                   pattern="BK-\d{8}-\d{3}" title="Format: BK-YYYYMMDD-XXX">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search me-2"></i>Track
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            Enter the booking code you received when making your booking (format: BK-YYYYMMDD-XXX)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fas fa-info-circle me-2"></i>Need Help?
                                    </h6>
                                    <p class="mb-0">
                                        Can't find your booking code? Check your email or SMS for the booking confirmation.
                                        If you still can't find it, please contact our support team.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
