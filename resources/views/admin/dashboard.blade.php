@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<!-- Quick Navigation -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-light border">
            <strong>Quick Links:</strong>
            <a href="#logo-favicon-section" class="btn btn-primary btn-sm ml-2">
                <i class="fas fa-image"></i> Logo & Favicon Settings
            </a>
            <a href="#workshop-location-section" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-map-marker-alt"></i> Workshop Location
            </a>
        </div>
    </div>
</div>

<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Users</span>
                <span class="info-box-number">{{ $stats['total_users'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Bookings</span>
                <span class="info-box-number">{{ $stats['total_bookings'] }}</span>
            </div>
        </div>
    </div>

    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Bookings</span>
                <span class="info-box-number">{{ $stats['pending_bookings'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completed</span>
                <span class="info-box-number">{{ $stats['completed_bookings'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <!-- Monthly Bookings Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Monthly Bookings
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Recent Bookings
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->customer_name }}</td>
                                <td>{{ $booking->service_type_label }}</td>
                                <td>
                                    <span class="badge badge-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'info') }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No bookings found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{-- <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye mr-1"></i> View All Bookings
                </a> --}}
                <span class="text-muted">Booking management coming soon</span>
            </div>
        </div>
    </div>

    <!-- Right col -->
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                {{-- <a href="{{ route('admin.bookings.create') }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-plus mr-1"></i> New Booking
                </a> --}}
                <span class="btn btn-success btn-block mb-2 disabled">
                    <i class="fas fa-plus mr-1"></i> New Booking (Coming Soon)
                </span>
                {{-- <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-user-plus mr-1"></i> Add User
                </a> --}}
                <span class="btn btn-primary btn-block mb-2 disabled">
                    <i class="fas fa-user-plus mr-1"></i> Add User (Coming Soon)
                </span>
                {{-- <a href="{{ route('admin.call-fees.create') }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-dollar-sign mr-1"></i> Add Call Fee
                </a> --}}
                <span class="btn btn-info btn-block mb-2 disabled">
                    <i class="fas fa-dollar-sign mr-1"></i> Add Call Fee (Coming Soon)
                </span>
                <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-cogs mr-1"></i> App Settings
                </a>
            </div>
        </div>

        <!-- Workshop Location Settings -->
        <div class="card" id="workshop-location-section">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Workshop Location
                </h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.dashboard.update-workshop-location') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="address">Workshop Address</label>
                        <input type="text" class="form-control" id="address" name="address"
                               value="{{ $workshopLocation['address'] }}" required>
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" step="0.000001" class="form-control" id="latitude" name="latitude"
                               value="{{ $workshopLocation['latitude'] }}" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" step="0.000001" class="form-control" id="longitude" name="longitude"
                               value="{{ $workshopLocation['longitude'] }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Update Location
                    </button>
                </form>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle"></i> Koordinat ini akan digunakan sebagai pusat peta dan titik awal perhitungan jarak.
                </small>
            </div>
        </div>

        <!-- Logo & Favicon Settings -->
        <div class="card border-primary" id="logo-favicon-section">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-image mr-1"></i>
                    <strong>Logo & Favicon Settings</strong>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Upload Logo & Favicon:</strong> Gunakan form di bawah ini untuk mengubah logo website dan favicon yang muncul di tab browser.
                </div>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.dashboard.update-logo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="logo">Site Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="text-muted">Upload logo website (PNG, JPG, JPEG, GIF, SVG - Max 2MB)</small>
                        @if(\App\Models\AppSetting::getValue('site_logo'))
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . \App\Models\AppSetting::getValue('site_logo')) }}" alt="Current Logo" style="max-height: 50px;">
                                <small class="text-muted d-block">Logo saat ini</small>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="favicon">Favicon</label>
                        <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*,.ico">
                        <small class="text-muted">Upload favicon (PNG, JPG, JPEG, GIF, ICO - Max 1MB)</small>
                        @if(\App\Models\AppSetting::getValue('site_favicon'))
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . \App\Models\AppSetting::getValue('site_favicon')) }}" alt="Current Favicon" style="max-height: 32px;">
                                <small class="text-muted d-block">Favicon saat ini</small>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-upload mr-1"></i> Update Logo & Favicon
                    </button>
                </form>
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle"></i> Logo akan ditampilkan di header website, favicon di tab browser.
                </small>
            </div>
        </div>

        <!-- System Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i>
                    System Info
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{ $stats['total_call_fees'] }}</h5>
                            <span class="description-text">CALL FEES</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header">{{ number_format($stats['total_bookings'] / max($stats['total_users'], 1), 1) }}</h5>
                            <span class="description-text">AVG BOOKINGS/USER</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function () {
    // Monthly Bookings Chart
    var ctx = document.getElementById('monthlyChart').getContext('2d');
    var monthlyData = @json($monthlyStats);

    // Fill missing months with 0
    var fullMonthlyData = [];
    for (var i = 1; i <= 12; i++) {
        fullMonthlyData.push(monthlyData[i] || 0);
    }

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Bookings',
                data: fullMonthlyData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
