@extends('admin.layout')

@section('title', 'Bookings Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Bookings</h3>
        <div class="card-tools">
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add Booking
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total Amount</th>
                        <th>Booking Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <strong>{{ $booking->booking_code }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $booking->customer_name }}</strong><br>
                                <small class="text-muted">{{ $booking->customer_phone }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $booking->getServiceTypeLabelAttribute() }}</strong><br>
                                <small class="text-muted">{{ $booking->motor_brand }} {{ $booking->motor_type }} ({{ $booking->motor_year }})</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">
                                {{ $booking->getStatusLabelAttribute() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'failed' ? 'danger' : 'warning') }}">
                                {{ $booking->getPaymentStatusLabelAttribute() }}
                            </span>
                        </td>
                        <td>
                            <strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            {{ $booking->booking_date ? $booking->booking_date->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this booking?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            No bookings found. <a href="{{ route('admin.bookings.create') }}">Create one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
