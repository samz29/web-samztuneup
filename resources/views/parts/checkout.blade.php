@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Checkout Spare Part
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Temporary Notice -->
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Pemberitahuan:</strong> Penjualan sparepart sementara dihentikan untuk perbaikan sistem. Silakan hubungi workshop untuk informasi lebih lanjut.
                    </div>

                    <form action="{{ route('part.order', $part) }}" method="POST" id="checkoutForm">
                        @csrf

                        <div class="row">
                            <!-- Product Summary -->
                            <div class="col-lg-4">
                                <div class="card border-primary">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Ringkasan Pesanan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex mb-3">
                                            <img src="{{ asset('storage/app/public/' . $part->image) }}" class="rounded me-3" alt="{{ $part->name }}" style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $part->name }}</h6>
                                                <p class="text-muted small mb-1">{{ Str::limit($part->description, 40) }}</p>
                                                <p class="fw-bold text-primary mb-0">Rp {{ number_format($part->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>

                                        <div class="border-top pt-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Jumlah:</span>
                                                <span id="quantity-display">{{ $quantity }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Harga Satuan:</span>
                                                <span>Rp {{ number_format($part->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Subtotal:</span>
                                                <span id="subtotal-display">Rp {{ number_format($part->price * $quantity, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total:</span>
                                                <span id="total-display">Rp {{ number_format($part->price * $quantity, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Information -->
                            <div class="col-lg-8">
                                <!-- Customer Details -->
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-user me-2"></i>Informasi Pembeli
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                                            @error('customer_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                                            @error('customer_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Shipping Address -->
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Pengiriman
                                    </h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="shipping_address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address') }}</textarea>
                                            @error('shipping_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_city" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required>
                                            @error('shipping_city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_postal_code" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_postal_code') is-invalid @enderror" id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" required>
                                            @error('shipping_postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Shipping Method -->
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-truck me-2"></i>Pilih Kurir & Layanan
                                    </h5>
                                    <div id="shipping-options" class="row">
                                        <div class="col-12">
                                            <p class="text-muted">Masukkan alamat pengiriman untuk melihat pilihan kurir</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-credit-card me-2"></i>Metode Pembayaran
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="payment_method" id="tripay" value="tripay" checked>
                                                <label class="form-check-label" for="tripay">
                                                    <strong>QRIS/Tripay</strong><br>
                                                    <small class="text-muted">Pembayaran instan via QRIS, transfer bank, e-wallet</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="payment_method" id="midtrans" value="midtrans">
                                                <label class="form-check-label" for="midtrans">
                                                    <strong>Midtrans</strong><br>
                                                    <small class="text-muted">Pembayaran via kartu kredit, transfer bank, e-wallet</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('part.show', $part) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedShipping = null;
let currentQuantity = {{ $quantity }};
let unitPrice = {{ $part->price }};

function updateTotals() {
    const subtotal = unitPrice * currentQuantity;
    const shippingCost = selectedShipping ? selectedShipping.cost : 0;
    const total = subtotal + shippingCost;

    document.getElementById('quantity-display').textContent = currentQuantity;
    document.getElementById('subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function calculateShipping() {
    const address = document.getElementById('shipping_address').value;
    const city = document.getElementById('shipping_city').value;
    const postalCode = document.getElementById('shipping_postal_code').value;

    if (!address || !city || !postalCode) {
        return;
    }

    // Show loading
    document.getElementById('shipping-options').innerHTML = `
        <div class="col-12 text-center">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <span class="ms-2">Menghitung ongkos kirim...</span>
        </div>
    `;

    // Prepare data
    const data = {
        part_name: '{{ $part->name }}',
        part_description: '{{ $part->description }}',
        part_price: unitPrice,
        quantity: currentQuantity,
        address: address,
        city: city,
        postal_code: postalCode
    };

    fetch('{{ route("part.calculate.shipping") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.rates) {
            displayShippingOptions(data.rates);
        } else {
            document.getElementById('shipping-options').innerHTML = `
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Gagal menghitung ongkos kirim. Silakan coba lagi.'}
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('shipping-options').innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Terjadi kesalahan saat menghitung ongkos kirim.
                </div>
            </div>
        `;
    });
}

function displayShippingOptions(rates) {
    let html = '<div class="col-12"><p class="mb-3 fw-semibold">Pilih layanan pengiriman:</p>';

    rates.forEach((rate, index) => {
        const isSelected = index === 0; // Select first option by default
        if (isSelected) {
            selectedShipping = {
                courier: rate.courier_name,
                service: rate.service_name,
                cost: rate.price
            };
        }

        html += `
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="shipping_option"
                       id="shipping_${index}" value="${rate.courier_name}|${rate.service_name}|${rate.price}"
                       ${isSelected ? 'checked' : ''}>
                <label class="form-check-label d-flex justify-content-between w-100" for="shipping_${index}">
                    <span>
                        <strong>${rate.courier_name} - ${rate.service_name}</strong><br>
                        <small class="text-muted">Estimasi: ${rate.estimated_delivery}</small>
                    </span>
                    <span class="fw-bold text-primary">Rp ${rate.price.toLocaleString('id-ID')}</span>
                </label>
            </div>
        `;
    });

    html += '</div>';
    document.getElementById('shipping-options').innerHTML = html;

    // Add event listeners
    document.querySelectorAll('input[name="shipping_option"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const [courier, service, cost] = this.value.split('|');
            selectedShipping = {
                courier: courier,
                service: service,
                cost: parseInt(cost)
            };
            updateTotals();

            // Update hidden inputs
            updateHiddenShippingInputs();
        });
    });

    updateTotals();
}

function updateHiddenShippingInputs() {
    // Remove existing hidden inputs
    document.querySelectorAll('input[name="shipping_courier"], input[name="shipping_service"], input[name="shipping_cost"]').forEach(input => input.remove());

    if (selectedShipping) {
        const form = document.getElementById('checkoutForm');

        const courierInput = document.createElement('input');
        courierInput.type = 'hidden';
        courierInput.name = 'shipping_courier';
        courierInput.value = selectedShipping.courier;
        form.appendChild(courierInput);

        const serviceInput = document.createElement('input');
        serviceInput.type = 'hidden';
        serviceInput.name = 'shipping_service';
        serviceInput.value = selectedShipping.service;
        form.appendChild(serviceInput);

        const costInput = document.createElement('input');
        costInput.type = 'hidden';
        costInput.name = 'shipping_cost';
        costInput.value = selectedShipping.cost;
        form.appendChild(costInput);
    }
}

// Event listeners
document.getElementById('shipping_address').addEventListener('blur', calculateShipping);
document.getElementById('shipping_city').addEventListener('blur', calculateShipping);
document.getElementById('shipping_postal_code').addEventListener('blur', calculateShipping);

// Form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (!selectedShipping) {
        e.preventDefault();
        alert('Silakan pilih layanan pengiriman terlebih dahulu.');
        return;
    }

    updateHiddenShippingInputs();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
});

// Initialize
updateTotals();

// Temporarily disable form submission for parts sales hold
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Penjualan sparepart sementara dihentikan. Silakan hubungi workshop untuk informasi lebih lanjut.');
    return false;
});

// Disable submit button
document.getElementById('submitBtn').disabled = true;
document.getElementById('submitBtn').innerHTML = 'Sementara Tidak Tersedia';
</script>
@endpush
