@extends('admin.layout')

@section('title', 'Booking Details - ' . $booking->booking_code)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Details</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-user mr-2"></i>Customer Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Name:</th>
                                <td>{{ $booking->customer_name }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $booking->customer_phone }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $booking->customer_email ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $booking->getFullAddressAttribute() }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-motorcycle mr-2"></i>Motorcycle Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Type:</th>
                                <td>{{ $booking->motor_type }}</td>
                            </tr>
                            <tr>
                                <th>Brand:</th>
                                <td>{{ $booking->motor_brand }}</td>
                            </tr>
                            <tr>
                                <th>Year:</th>
                                <td>{{ $booking->motor_year }}</td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $booking->motor_description ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-wrench mr-2"></i>Service Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Service Type:</th>
                                <td><strong>{{ $booking->getServiceTypeLabelAttribute() }}</strong></td>
                            </tr>
                            <tr>
                                <th>Base Price:</th>
                                <td>Rp {{ number_format($booking->base_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Call Fee:</th>
                                <td>Rp {{ number_format($booking->call_fee, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Distance:</th>
                                <td>{{ $booking->distance_km }} km</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-calendar mr-2"></i>Booking Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Booking Code:</th>
                                <td><strong>{{ $booking->booking_code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Booking Date:</th>
                                <td>{{ $booking->booking_date ? $booking->booking_date->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge badge-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ $booking->getStatusLabelAttribute() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Estimated Duration:</th>
                                <td>{{ $booking->estimated_duration ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-credit-card mr-2"></i>Payment Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Payment Method:</th>
                                <td>{{ ucfirst($booking->payment_method) }}</td>
                            </tr>
                            <tr>
                                <th>Payment Status:</th>
                                <td>
                                    <span class="badge badge-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                        {{ $booking->getPaymentStatusLabelAttribute() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>DP Amount:</th>
                                <td>Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount:</th>
                                <td><strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-info-circle mr-2"></i>Additional Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Notes:</th>
                                <td>{{ $booking->notes ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>TriPay Reference:</th>
                                <td>{{ $booking->tripay_reference ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>TriPay URL:</th>
                                <td>
                                    @if($booking->tripay_url)
                                        <a href="{{ $booking->tripay_url }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-external-link-alt mr-1"></i> View Payment
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="status">Update Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="new" {{ $booking->status === 'new' ? 'selected' : '' }}>Baru</option>
                            <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="on_the_way" {{ $booking->status === 'on_the_way' ? 'selected' : '' }}>Dalam Perjalanan</option>
                            <option value="in_progress" {{ $booking->status === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="payment_status">Update Payment Status</label>
                        <select name="payment_status" id="payment_status" class="form-control">
                            <option value="pending" {{ $booking->payment_status === 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="failed" {{ $booking->payment_status === 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="cancelled" {{ $booking->payment_status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
