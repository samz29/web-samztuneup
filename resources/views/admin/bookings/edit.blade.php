@extends('admin.layout')

@section('title', 'Edit Booking - ' . $booking->booking_code)

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Booking</h3>
        <div class="card-tools">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye mr-1"></i> View Details
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>
    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-user mr-2"></i>Customer Information</h5>
                    <div class="form-group">
                        <label for="customer_name">Customer Name *</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Phone *</label>
                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone) }}" required>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email</label>
                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email', $booking->customer_email) }}">
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="customer_address">Address *</label>
                        <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" rows="3" required>{{ old('customer_address', $booking->customer_address) }}</textarea>
                        @error('customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_city">City *</label>
                                <input type="text" class="form-control @error('customer_city') is-invalid @enderror" id="customer_city" name="customer_city" value="{{ old('customer_city', $booking->customer_city) }}" required>
                                @error('customer_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="customer_district">District</label>
                                <input type="text" class="form-control @error('customer_district') is-invalid @enderror" id="customer_district" name="customer_district" value="{{ old('customer_district', $booking->customer_district) }}">
                                @error('customer_district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="customer_postal_code">Postal Code</label>
                                <input type="text" class="form-control @error('customer_postal_code') is-invalid @enderror" id="customer_postal_code" name="customer_postal_code" value="{{ old('customer_postal_code', $booking->customer_postal_code) }}">
                                @error('customer_postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5><i class="fas fa-motorcycle mr-2"></i>Motorcycle Information</h5>
                    <div class="form-group">
                        <label for="motor_type">Motor Type *</label>
                        <input type="text" class="form-control @error('motor_type') is-invalid @enderror" id="motor_type" name="motor_type" value="{{ old('motor_type', $booking->motor_type) }}" required>
                        @error('motor_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="motor_brand">Motor Brand *</label>
                        <input type="text" class="form-control @error('motor_brand') is-invalid @enderror" id="motor_brand" name="motor_brand" value="{{ old('motor_brand', $booking->motor_brand) }}" required>
                        @error('motor_brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="motor_year">Motor Year *</label>
                        <input type="number" class="form-control @error('motor_year') is-invalid @enderror" id="motor_year" name="motor_year" value="{{ old('motor_year', $booking->motor_year) }}" required>
                        @error('motor_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="motor_description">Motor Description</label>
                        <textarea class="form-control @error('motor_description') is-invalid @enderror" id="motor_description" name="motor_description" rows="3">{{ old('motor_description', $booking->motor_description) }}</textarea>
                        @error('motor_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-wrench mr-2"></i>Service Information</h5>
                    <div class="form-group">
                        <label for="service_type">Service Type *</label>
                        <select class="form-control @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                            <option value="">Select Service Type</option>
                            <option value="remap_ecu" {{ old('service_type', $booking->service_type) === 'remap_ecu' ? 'selected' : '' }}>Remap ECU Standar</option>
                            <option value="custom_tune" {{ old('service_type', $booking->service_type) === 'custom_tune' ? 'selected' : '' }}>Custom Tune</option>
                            <option value="dyno_tune" {{ old('service_type', $booking->service_type) === 'dyno_tune' ? 'selected' : '' }}>Dyno Tune</option>
                            <option value="full_package" {{ old('service_type', $booking->service_type) === 'full_package' ? 'selected' : '' }}>Full Package (Remap + Dyno)</option>
                        </select>
                        @error('service_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="base_price">Base Price *</label>
                        <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price', $booking->base_price) }}" required>
                        @error('base_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="distance_km">Distance (km) *</label>
                        <input type="number" step="0.01" class="form-control @error('distance_km') is-invalid @enderror" id="distance_km" name="distance_km" value="{{ old('distance_km', $booking->distance_km) }}" required>
                        @error('distance_km')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h5><i class="fas fa-calendar mr-2"></i>Booking Information</h5>
                    <div class="form-group">
                        <label for="booking_date">Booking Date</label>
                        <input type="datetime-local" class="form-control @error('booking_date') is-invalid @enderror" id="booking_date" name="booking_date" value="{{ old('booking_date', $booking->booking_date ? $booking->booking_date->format('Y-m-d\TH:i') : '') }}">
                        @error('booking_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="new" {{ old('status', $booking->status) === 'new' ? 'selected' : '' }}>Baru</option>
                            <option value="confirmed" {{ old('status', $booking->status) === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="on_the_way" {{ old('status', $booking->status) === 'on_the_way' ? 'selected' : '' }}>Dalam Perjalanan</option>
                            <option value="in_progress" {{ old('status', $booking->status) === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="completed" {{ old('status', $booking->status) === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ old('status', $booking->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="estimated_duration">Estimated Duration</label>
                        <input type="text" class="form-control @error('estimated_duration') is-invalid @enderror" id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $booking->estimated_duration) }}" placeholder="e.g., 2-3 hours">
                        @error('estimated_duration')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-credit-card mr-2"></i>Payment Information</h5>
                    <div class="form-group">
                        <label for="payment_method">Payment Method *</label>
                        <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="full" {{ old('payment_method', $booking->payment_method) === 'full' ? 'selected' : '' }}>Full Payment</option>
                            <option value="dp" {{ old('payment_method', $booking->payment_method) === 'dp' ? 'selected' : '' }}>Down Payment</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Payment Status *</label>
                        <select class="form-control @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                            <option value="pending" {{ old('payment_status', $booking->payment_status) === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="paid" {{ old('payment_status', $booking->payment_status) === 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="failed" {{ old('payment_status', $booking->payment_status) === 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="cancelled" {{ old('payment_status', $booking->payment_status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="dp_amount">DP Amount</label>
                        <input type="number" step="0.01" class="form-control @error('dp_amount') is-invalid @enderror" id="dp_amount" name="dp_amount" value="{{ old('dp_amount', $booking->dp_amount) }}">
                        @error('dp_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h5><i class="fas fa-info-circle mr-2"></i>Additional Information</h5>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude', $booking->latitude) }}">
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude', $booking->longitude) }}">
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Update Booking
            </button>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
