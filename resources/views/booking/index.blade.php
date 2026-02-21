@extends('layouts.app')

@section('title', 'SamzTune-Up - Professional ECU Tuning Services')

@section('content')
    <!-- Promo Slider Section -->
    @if($promoSliders->count())
    <div class="row justify-content-center mb-4">
        <div class="col-12">
            <div id="promoSlider" class="carousel slide rounded-4 overflow-hidden shadow mb-3" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($promoSliders as $i => $slider)
                    <div class="carousel-item @if($i === 0) active @endif">
                        <div class="d-flex flex-column flex-md-row align-items-center bg-dark text-white p-4 p-md-5 rounded-4">
                            @if($slider->image)
                                <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" class="img-fluid me-md-4 mb-3 mb-md-0 shadow-lg" style="max-width:340px; max-height:180px; object-fit:cover; border-radius:16px;">
                            @endif
                            <div>
                                <h3 class="fw-bold mb-2 display-6">{{ $slider->title }}</h3>
                                @if($slider->subtitle)
                                    <h5 class="mb-2 text-warning">{{ $slider->subtitle }}</h5>
                                @endif
                                <p class="mb-3">{{ $slider->description }}</p>
                                @if($slider->button_text && $slider->button_url)
                                    <a href="{{ $slider->button_url }}" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm" target="_blank">{{ $slider->button_text }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($promoSliders->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#promoSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#promoSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <section class="mb-5">
        <div class="hero-section bg-linear-to-r from-dark to-gray-900 rounded-4 shadow-lg overflow-hidden">
            <div class="hero-content p-4 p-md-5 text-center text-white">
                <div class="hero-icon mb-4">
                    <i class="fas fa-motorcycle fa-4x text-warning"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3 text-shadow">Motor Standar Tapi Terasa "Nahan"?</h1>
                <p class="lead mb-4 text-light opacity-75">Buka limit mesin motor Anda tanpa bongkar mesin. Lebih responsif, lebih bertenaga, dan tetap aman untuk harian.</p>
                <div class="hero-buttons d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="{{ route('booking.create') }}" class="btn btn-warning btn-lg px-4 py-3 fw-bold text-dark shadow rounded-pill">
                        <i class="fas fa-rocket me-2"></i>Booking Sekarang
                    </a>
                    <a href="#services" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold rounded-pill">
                        <i class="fas fa-info-circle me-2"></i>Pelajari Layanan
                    </a>
                </div>
            </div>
            <div class="hero-bg-pattern">
                <div class="pattern-overlay"></div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="mb-5">
        <h2 class="text-center mb-4 fw-bold display-6">Layanan Kami</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 bg-dark text-white border-0 shadow rounded-4">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">ECU Remap</h5>
                        <p class="card-text">Optimize your engine performance with professional ECU remapping.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 bg-dark text-white border-0 shadow rounded-4">
                    <div class="card-body text-center">
                        <i class="fas fa-tachometer-alt fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Custom Tuning</h5>
                        <p class="card-text">Tailored performance tuning for your specific driving needs.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 bg-dark text-white border-0 shadow rounded-4">
                    <div class="card-body text-center">
                        <i class="fas fa-flask fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Dyno Testing</h5>
                        <p class="card-text">Precise performance measurement and optimization.</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 bg-dark text-white border-0 shadow rounded-4">
                    <div class="card-body text-center">
                        <i class="fas fa-tools fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Full Package</h5>
                        <p class="card-text">Complete tuning solution with all services included.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4 fw-bold display-6">Kenapa Pilih SamzTune-Up?</h2>
        <div class="bento-grid">
            <div class="bento-card bento-primary">
                <div class="bento-icon"><i class="fas fa-cogs text-primary"></i></div>
                <h3 class="bento-title">Professional ECU Tuning</h3>
                <p class="bento-text">Expert tuning services for Honda motorcycles. Unlock your engine's true potential with advanced ECU remapping technology.</p>
            </div>
            <div class="bento-card bento-success">
                <div class="bento-icon"><i class="fas fa-tools text-success"></i></div>
                <h3 class="bento-title">No Engine Dismantling</h3>
                <p class="bento-text">We perform all tuning without removing your engine. Safe, clean, and efficient service at your location.</p>
            </div>
            <div class="bento-card bento-warning">
                <div class="bento-icon"><i class="fas fa-shield-alt text-warning"></i></div>
                <h3 class="bento-title">Daily Safe Tuning</h3>
                <p class="bento-text">Tuned for daily use with safety in mind. More responsive throttle and acceleration without compromising reliability.</p>
            </div>
            <div class="bento-card bento-info">
                <div class="bento-icon"><i class="fas fa-truck text-info"></i></div>
                <h3 class="bento-title">Mobile Service</h3>
                <p class="bento-text">We come to you! Professional mobile tuning service at your preferred location with full equipment.</p>
            </div>
        </div>
    </section>

    <!-- Service Fees Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4 fw-bold display-6">Biaya Layanan</h2>
        <div class="row g-4 justify-content-center">
            @foreach($callFees as $fee)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 bg-dark text-white border-0 shadow rounded-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary fw-bold">{{ $fee->name }}</h5>
                        <p class="card-text text-muted mb-2">
                            <i class="fas fa-route me-1"></i>
                            Distance: {{ $fee->min_distance }} - {{ $fee->max_distance }} km
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h4 text-success fw-bold mb-0">Rp {{ number_format($fee->fee, 0, ',', '.') }}</span>
                            <small class="text-muted">per service</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4 fw-bold display-6">Galeri SamzTune-Up</h2>
        <div class="row g-3 justify-content-center">
            @forelse($galleries as $gallery)
                <div class="col-6 col-md-4 col-lg-3">
                    @php
                        // prefer explicit public_html/storage/app/public location per request
                        $imgPath = 'storage/' . $gallery->image;

                        if (! (
                            file_exists(public_path($imgPath)) ||
                            file_exists(storage_path('app/public/' . $gallery->image))
                        )) {
                            // fallback placeholder
                            $imgPath = 'storage/logos/default.png';
                        }
                    @endphp
                    <img src="{{ asset($imgPath) }}" class="img-fluid rounded-4 shadow-sm" alt="{{ $gallery->caption }}">
                    @if($gallery->caption)
                        <div class="small text-center text-muted mt-1">{{ $gallery->caption }}</div>
                    @endif
                </div>
            @empty
                <div class="col-12 text-center text-muted">Belum ada foto galeri.</div>
            @endforelse
        </div>
    </section>

    <!-- Sponsors Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4 fw-bold display-6">Didukung Oleh</h2>
        <div class="row g-3 justify-content-center">
            @forelse($sponsors as $sponsor)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-3">
                            <img src="{{ asset('storage/' . $sponsor->image) }}" class="img-fluid rounded-3" alt="{{ $sponsor->name }}" style="max-height: 120px; object-fit: contain;">
                            <h6 class="mt-2 mb-0">{{ $sponsor->name }}</h6>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">Belum ada sponsor.</div>
            @endforelse
        </div>
    </section>

    <!-- Parts Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4 fw-bold display-6">Spare Parts</h2>
        <div class="row g-3 justify-content-center">
            @forelse($parts as $part)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . $part->image) }}" class="card-img-top rounded-top" alt="{{ $part->name }}" style="height: 180px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $part->name }}</h6>
                            @if($part->description)
                                <p class="card-text small text-muted">{{ Str::limit($part->description, 60) }}</p>
                            @endif
                            @if($part->price)
                                <p class="card-text fw-bold text-primary">Rp {{ number_format($part->price, 0, ',', '.') }}</p>
                            @endif
                            <a href="{{ route('part.show', $part) }}" class="btn btn-primary btn-sm mt-auto">
                                <i class="fas fa-shopping-cart me-1"></i>Beli
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">Belum ada spare parts.</div>
            @endforelse
        </div>
    </section>

    <!-- CTA Section -->
    <section>
        <div class="bg-dark text-white p-4 p-md-5 text-center rounded-4 shadow">
            <h3 class="mb-3">Ready to Boost Your Vehicle's Performance?</h3>
            <p class="mb-4">Book your tuning service today and experience the difference professional ECU tuning can make.</p>
            <a href="{{ route('booking.create') }}" class="btn btn-primary btn-lg rounded-pill">
                <i class="fas fa-rocket me-2"></i>Start Booking Process
            </a>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    .bento-card {
        background: #23272b;
        border-radius: 18px;
        padding: 2rem 1.5rem;
        text-align: center;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }
    .bento-card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 12px 32px rgba(0,0,0,0.18);
    }
    .bento-icon {
        font-size: 2.5rem;
        margin-bottom: 1.2rem;
    }
    .bento-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.7rem;
    }
    .bento-text {
        color: #e0e0e0;
        font-size: 0.97rem;
    }
    .carousel-inner img {
        object-fit: cover;
    }
    .card {
        border-radius: 18px !important;
    }
    .rounded-4 {
        border-radius: 1.25rem !important;
    }
    .shadow {
        box-shadow: 0 4px 24px rgba(0,0,0,0.10) !important;
    }
    .shadow-lg {
        box-shadow: 0 8px 32px rgba(0,0,0,0.18) !important;
    }
    .shadow-sm {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    }
</style>
@endpush
