@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-motorcycle fa-2x me-3"></i>
                        <div>
                            <h3 class="mb-0">Booking Jasa Remap Motor Panggilan</h3>
                            <small class="d-block">SamzTune UP - Teknisi Datang ke Lokasi Anda</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST">
                        @csrf

                        <!-- Customer Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Customer</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                                               value="{{ old('customer_name') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_phone" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                               value="{{ old('customer_phone') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email"
                                               value="{{ old('customer_email') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information (Khusus Jasa Panggilan) -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Panggilan</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Teknisi SamzTune UP akan datang ke lokasi Anda. Pastikan alamat lengkap dan akurat.
                                </div>

                                <!-- Map Section -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Pilih Lokasi di Peta</label>
                                    <div class="position-relative mb-2">
                                        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama jalan atau gedung..." autocomplete="off">
                                        <ul id="suggestionsList" class="list-group position-absolute w-100 shadow" style="z-index: 1000; display:none; max-height: 200px; overflow-y: auto;"></ul>
                                    </div>
                                    <div id="mapContainer" style="width: 100%; height: 400px; background: #e9ecef; border-radius: 8px;"></div>
                                    <div class="form-text text-muted"><i class="fas fa-info-circle"></i> Geser pin merah untuk menentukan lokasi yang akurat.</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required>{{ old('customer_address') }}</textarea>
                                        <small class="form-text text-muted">Contoh: Jl. Merdeka No. 123, RT 05/RW 02</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_district" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_district" name="customer_district"
                                               value="{{ old('customer_district') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_city" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_city" name="customer_city"
                                               value="{{ old('customer_city') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_postal_code" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="customer_postal_code" name="customer_postal_code"
                                               value="{{ old('customer_postal_code') }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" id="calculateFeeBtn" class="btn btn-outline-primary">
                                        <i class="fas fa-calculator me-2"></i>Hitung Biaya Panggilan
                                    </button>
                                    <div id="feeResult" class="mt-3 d-none">
                                        <div class="alert alert-success">
                                            <h6><i class="fas fa-route me-2"></i>Estimasi Jarak & Biaya</h6>
                                            <p id="distanceResult"></p>
                                            <p id="callFeeResult"></p>
                                            <p id="totalAmountResult" class="fw-bold fs-5"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Motor Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Informasi Motor</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="motor_brand" class="form-label">Merek Motor <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="motor_brand" name="motor_brand"
                                               value="{{ old('motor_brand') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="motor_type" class="form-label">Tipe/Model <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="motor_type" name="motor_type"
                                               value="{{ old('motor_type') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="motor_year" class="form-label">Tahun <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="motor_year" name="motor_year"
                                               value="{{ old('motor_year') }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="motor_description" class="form-label">Keterangan Tambahan (Modifikasi, ECU type, dll)</label>
                                    <textarea class="form-control" id="motor_description" name="motor_description" rows="3">{{ old('motor_description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Pilih Layanan Remap</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php $prices = \App\Models\Booking::getServicePrices(); @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check border p-3 rounded">
                                            <input class="form-check-input" type="radio" name="service_type" id="remap_ecu"
                                                   value="remap_ecu" required>
                                            <label class="form-check-label w-100" for="remap_ecu">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Remap ECU Standar</strong>
                                                    <span class="badge bg-primary">Rp {{ number_format($prices['remap_ecu'], 0, ',', '.') }}</span>
                                                </div>
                                                <small class="text-muted">Optimasi performa standar untuk harian</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check border p-3 rounded">
                                            <input class="form-check-input" type="radio" name="service_type" id="custom_tune"
                                                   value="custom_tune">
                                            <label class="form-check-label w-100" for="custom_tune">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Custom Tune</strong>
                                                    <span class="badge bg-primary">Rp {{ number_format($prices['custom_tune'], 0, ',', '.') }}</span>
                                                </div>
                                                <small class="text-muted">Tuning khusus sesuai kebutuhan & modifikasi</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check border p-3 rounded">
                                            <input class="form-check-input" type="radio" name="service_type" id="dyno_tune"
                                                   value="dyno_tune">
                                            <label class="form-check-label w-100" for="dyno_tune">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Dyno Tune</strong>
                                                    <span class="badge bg-primary">Rp {{ number_format($prices['dyno_tune'], 0, ',', '.') }}</span>
                                                </div>
                                                <small class="text-muted">Tuning dengan dyno untuk hasil maksimal</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check border p-3 rounded">
                                            <input class="form-check-input" type="radio" name="service_type" id="full_package"
                                                   value="full_package">
                                            <label class="form-check-label w-100" for="full_package">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Full Package</strong>
                                                    <span class="badge bg-primary">Rp {{ number_format($prices['full_package'], 0, ',', '.') }}</span>
                                                </div>
                                                <small class="text-muted">Remap + Dyno Tune + Garansi</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Metode Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check border p-3 rounded h-100">
                                            <input class="form-check-input" type="radio" name="payment_method" id="tripay"
                                                   value="tripay" required>
                                            <label class="form-check-label" for="tripay">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-credit-card fa-2x text-primary me-2"></i>
                                                    <strong>Bayar Online</strong>
                                                </div>
                                                <small>Lunas sebelum teknisi datang via Tripay (QRIS, Transfer, dll)</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check border p-3 rounded h-100">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                                   value="cod">
                                            <label class="form-check-label" for="cod">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-money-bill-wave fa-2x text-success me-2"></i>
                                                    <strong>Bayar di Tempat</strong>
                                                </div>
                                                <small>Lunas saat teknisi selesai mengerjakan</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check border p-3 rounded h-100">
                                            <input class="form-check-input" type="radio" name="payment_method" id="dp"
                                                   value="dp">
                                            <label class="form-check-label" for="dp">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-hand-holding-usd fa-2x text-warning me-2"></i>
                                                    <strong>DP 50%</strong>
                                                </div>
                                                <small>DP 50% saat booking, sisanya saat selesai</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tripay Channel Selection -->
                                <div id="tripayChannelSection" class="mb-3 d-none">
                                    <label for="tripay_channel" class="form-label">Pilih Metode Pembayaran Tripay <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tripay_channel" name="tripay_channel">
                                        <option value="">-- Pilih Metode Pembayaran --</option>
                                        @foreach($paymentChannels as $channel)
                                            <option value="{{ $channel['code'] }}">
                                                {{ $channel['name'] }} - {{ $channel['description'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Schedule -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Jadwal Kedatangan Teknisi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="booking_date" class="form-label">Tanggal & Jam Kedatangan <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="booking_date" name="booking_date"
                                               min="{{ date('Y-m-d\TH:i', strtotime('+1 hour')) }}" required>
                                        <small class="form-text text-muted">Minimal 1 jam dari sekarang. Durasi pengerjaan ±2 jam</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan Tambahan</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="notes" rows="3" placeholder="Contoh: Parkir di depan rumah, ada garasi, dll">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Hidden fields for calculation -->
                        <input type="hidden" name="base_price" id="base_price" value="0">
                        <input type="hidden" name="call_fee" id="call_fee" value="0">
                        <input type="hidden" name="total_amount" id="total_amount" value="0">
                        <input type="hidden" name="latitude" id="latitude" value="0">
                        <input type="hidden" name="longitude" id="longitude" value="0">
                        <input type="hidden" name="distance_km" id="distance_km" value="0">

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>
                                <i class="fas fa-calendar-check me-2"></i>Booking Sekarang (Rp 0)
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
<style>
    .form-check-label {
        cursor: pointer;
    }
    .card {
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<script src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceTypeRadios = document.querySelectorAll('input[name="service_type"]');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    const tripayChannelSection = document.getElementById('tripayChannelSection');
    const tripayChannelSelect = document.getElementById('tripay_channel');
    const calculateFeeBtn = document.getElementById('calculateFeeBtn');
    const feeResult = document.getElementById('feeResult');
    const submitBtn = document.getElementById('submitBtn');

    // --- HERE Maps Integration ---
    const apiKey = '{{ $mapsApiKey }}';
    if (!apiKey) {
        document.getElementById('mapContainer').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-danger fw-bold">API Key HERE Maps belum diatur! Cek Controller/.env</div>';
    }

    const platform = new H.service.Platform({
        'apikey': apiKey
    });
    const defaultLayers = platform.createDefaultLayers();
    const service = platform.getSearchService();

    // Base Workshop Coords
    const baseLat = parseFloat("{{ $baseLat ?? config('services.samztune.base_latitude', -6.200000) }}") || -6.200000;
    const baseLng = parseFloat("{{ $baseLng ?? config('services.samztune.base_longitude', 106.816666) }}") || 106.816666;
    const basePoint = new H.geo.Point(baseLat, baseLng);
    try {
        const map = new H.Map(
            document.getElementById('mapContainer'),
            defaultLayers.vector.normal.map,
            {
                zoom: 13,
                center: { lat: baseLat, lng: baseLng },
                pixelRatio: window.devicePixelRatio || 1
            }
        );

    window.addEventListener('resize', () => map.getViewPort().resize());
    const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
    const ui = H.ui.UI.createDefault(map, defaultLayers);

    let mainMarker = null;

    function initMarker(lat, lng) {
        if (mainMarker) map.removeObject(mainMarker);
        mainMarker = new H.map.Marker({ lat: lat, lng: lng }, { volatility: true });
        mainMarker.draggable = true;
        map.addObject(mainMarker);
        updateLocationData(lat, lng);

        map.addEventListener('dragstart', (ev) => {
            if (ev.target instanceof H.map.Marker) behavior.disable();
        }, false);
        map.addEventListener('dragend', (ev) => {
            if (ev.target instanceof H.map.Marker) {
                behavior.enable();
                const coord = ev.target.getGeometry();
                updateLocationData(coord.lat, coord.lng);
            }
        }, false);
        map.addEventListener('drag', (ev) => {
            if (ev.target instanceof H.map.Marker) {
                const pointer = ev.currentPointer;
                ev.target.setGeometry(map.screenToGeo(pointer.viewportX - ev.target['offset'].x, pointer.viewportY - ev.target['offset'].y));
            }
        }, false);
    }

    function updateLocationData(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);

        const currentPoint = new H.geo.Point(lat, lng);
        const distanceMeters = basePoint.distance(currentPoint);
        const distanceKm = (distanceMeters / 1000).toFixed(2);
        document.getElementById('distance_km').value = distanceKm;

        reverseGeocode(lat, lng);
    }

    function reverseGeocode(lat, lng) {
        service.reverseGeocode({ at: lat + "," + lng }, (result) => {
            if (result.items.length > 0) {
                const item = result.items[0];
                const address = item.address;
                document.getElementById('customer_address').value = item.address.label;
                if(address.city) document.getElementById('customer_city').value = address.city;
                if(address.district) document.getElementById('customer_district').value = address.district;
                if(address.postalCode) document.getElementById('customer_postal_code').value = address.postalCode;
            }
        }, console.error);
    }

    // Autocomplete
    const searchInput = document.getElementById('searchInput');
    const suggestionsList = document.getElementById('suggestionsList');
    let debounceTimeout = null;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        clearTimeout(debounceTimeout);
        if (query.length < 3) { suggestionsList.style.display = 'none'; return; }

        debounceTimeout = setTimeout(() => {
            service.autosuggest({ q: query, at: map.getCenter().lat + "," + map.getCenter().lng, limit: 5 }, (result) => {
                suggestionsList.innerHTML = '';
                suggestionsList.style.display = 'block';
                result.items.forEach(item => {
                    if (item.position) {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action';
                        li.style.cursor = 'pointer';
                        li.textContent = item.title + " (" + (item.address.label || '') + ")";
                        li.onclick = () => {
                            map.setCenter(item.position);
                            map.setZoom(15);
                            initMarker(item.position.lat, item.position.lng);
                            suggestionsList.style.display = 'none';
                            searchInput.value = '';
                        };
                        suggestionsList.appendChild(li);
                    }
                });
            }, alert);
        }, 300);
    });

    initMarker(baseLat, baseLng);
    } catch (error) {
        console.error('Error initializing map:', error);
        document.getElementById('mapContainer').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-danger fw-bold">Error loading map: ' + error.message + '</div>';
    }

    // Toggle Tripay channel section
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'tripay') {
                tripayChannelSection.classList.remove('d-none');
                tripayChannelSelect.required = true;
            } else {
                tripayChannelSection.classList.add('d-none');
                tripayChannelSelect.required = false;
            }
        });
    });

    // Calculate fee when button clicked
    calculateFeeBtn.addEventListener('click', function() {
        const address = document.getElementById('customer_address').value;
        const district = document.getElementById('customer_district').value;
        const city = document.getElementById('customer_city').value;
        const postalCode = document.getElementById('customer_postal_code').value;
        const serviceType = document.querySelector('input[name="service_type"]:checked');

        if (!address || !district || !city || !postalCode) {
            alert('Lengkapi alamat terlebih dahulu!');
            return;
        }

        if (!serviceType) {
            alert('Pilih jenis layanan terlebih dahulu!');
            return;
        }

        calculateFeeBtn.disabled = true;
        calculateFeeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghitung...';

        fetch("{{ route('booking.calculate.fee') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                address: address,
                district: district,
                city: city,
                postal_code: postalCode,
                service_type: serviceType.value
            })
        })
        .then(response => response.json())
        .then(data => {
            calculateFeeBtn.disabled = false;
            calculateFeeBtn.innerHTML = '<i class="fas fa-calculator me-2"></i>Hitung Biaya Panggilan';

            if (data.success) {
                // Update hidden fields
                document.getElementById('base_price').value = data.data.base_price;
                document.getElementById('call_fee').value = data.data.call_fee;
                document.getElementById('total_amount').value = data.data.total_amount;
                document.getElementById('latitude').value = data.data.latitude;
                document.getElementById('longitude').value = data.data.longitude;
                document.getElementById('distance_km').value = data.data.distance_km;

                // Update UI
                document.getElementById('distanceResult').innerHTML =
                    `<strong>Jarak:</strong> ${data.data.distance_km} km dari base workshop`;
                document.getElementById('callFeeResult').innerHTML =
                    `<strong>Biaya Panggilan:</strong> Rp ${parseInt(data.data.call_fee).toLocaleString('id-ID')}`;
                document.getElementById('totalAmountResult').innerHTML =
                    `<strong>Total Pembayaran:</strong> Rp ${parseInt(data.data.total_amount).toLocaleString('id-ID')}`;

                feeResult.classList.remove('d-none');
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="fas fa-calendar-check me-2"></i>Booking Sekarang (Rp ${parseInt(data.data.total_amount).toLocaleString('id-ID')})`;
            } else {
                alert(data.message || 'Gagal menghitung biaya');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            calculateFeeBtn.disabled = false;
            calculateFeeBtn.innerHTML = '<i class="fas fa-calculator me-2"></i>Hitung Biaya Panggilan';
            alert('Terjadi kesalahan saat menghitung biaya');
        });
    });

    // Enable submit button only after fee calculated
    submitBtn.addEventListener('click', function(e) {
        const totalAmount = document.getElementById('total_amount').value;
        if (parseFloat(totalAmount) === 0) {
            e.preventDefault();
            alert('Hitung biaya panggilan terlebih dahulu!');
        }
    });
});
</script>
@endpush
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    if (!form) return;

    function showAlert(message) {
        let existing = document.getElementById('bookingValidationAlert');
        if (existing) existing.remove();
        const wrapper = document.createElement('div');
        wrapper.id = 'bookingValidationAlert';
        wrapper.className = 'alert alert-danger alert-dismissible fade show';
        wrapper.role = 'alert';
        wrapper.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        form.parentNode.insertBefore(wrapper, form);
        wrapper.scrollIntoView({behavior: 'smooth', block: 'center'});
    }

    form.addEventListener('submit', function(e) {
        const requiredFields = [
            'customer_name','customer_phone','customer_email','customer_address',
            'customer_city','customer_district','customer_postal_code',
            'motor_type','motor_brand','motor_year','service_type','booking_date'
        ];

        const missing = [];
        requiredFields.forEach(function(name) {
            const el = document.querySelector('[name="'+name+'"]');
            if (!el) return;
            const val = (el.value || '').toString().trim();
            if (val === '' || val === '0') missing.push(name.replace('_',' '));
        });

        // check lat/lng
        const lat = parseFloat(document.getElementById('latitude').value || 0);
        const lng = parseFloat(document.getElementById('longitude').value || 0);
        if (!lat || !lng) {
            missing.push('lokasi (geser pin pada peta)');
        }

        if (missing.length) {
            e.preventDefault();
            showAlert('Mohon isi terlebih dahulu: ' + missing.join(', '));
            return false;
        }

        return true;
    });
});
</script>
@endpush
