@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Slider Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-12">
            <div id="homeSlider" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="bg-dark text-white p-5 rounded shadow">
                            <h1 class="display-4 fw-bold mb-3">Motor Standar Tapi Terasa "Nahan"?</h1>
                            <p class="lead">Buka limit mesin Motor Anda tanpa bongkar mesin. Lebih responsif, lebih bertenaga, dan tetap aman untuk harian.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white">
                <div class="card-header bg-dark border-secondary">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    {{-- Bento Grid Section --}}
                    <div class="mt-4">
                        <h2 class="text-center mb-4 fw-bold">Dashboard Features</h2>
                        <div class="grid grid-cols-2 gap-4 max-w-4xl mx-auto">
                            <!-- Top Left -->
                            <div class="bg-gradient-to-br from-blue-800 to-blue-900 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-chart-line text-2xl text-blue-200 mr-3"></i>
                                    <h3 class="text-lg font-bold">Performance Analytics</h3>
                                </div>
                                <p class="text-blue-100 text-sm">Monitor your tuning results and vehicle performance metrics.</p>
                            </div>

                            <!-- Top Right -->
                            <div class="bg-gradient-to-br from-green-800 to-green-900 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-calendar-check text-2xl text-green-200 mr-3"></i>
                                    <h3 class="text-lg font-bold">Booking Management</h3>
                                </div>
                                <p class="text-green-100 text-sm">View and manage your service bookings and appointments.</p>
                            </div>

                            <!-- Bottom Left -->
                            <div class="bg-gradient-to-br from-purple-800 to-purple-900 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-history text-2xl text-purple-200 mr-3"></i>
                                    <h3 class="text-lg font-bold">Service History</h3>
                                </div>
                                <p class="text-purple-100 text-sm">Track all your previous tuning services and maintenance records.</p>
                            </div>

                            <!-- Bottom Right -->
                            <div class="bg-gradient-to-br from-red-800 to-red-900 text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-bell text-2xl text-red-200 mr-3"></i>
                                    <h3 class="text-lg font-bold">Notifications</h3>
                                </div>
                                <p class="text-red-100 text-sm">Stay updated with service reminders and important announcements.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
