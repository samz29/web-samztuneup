@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="row g-0">
                    <!-- Product Image -->
                    <div class="col-md-6">
                        <img src="{{ asset('storage/' . $part->image) }}" class="card-img-top rounded-start h-100" alt="{{ $part->name }}" style="object-fit: cover;">
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-6">
                        <div class="card-body">
                            <h2 class="card-title fw-bold mb-3">{{ $part->name }}</h2>

                            @if($part->description)
                                <p class="card-text text-muted mb-4">{{ $part->description }}</p>
                            @endif

                            @if($part->price)
                                <div class="mb-4">
                                    <h3 class="text-primary fw-bold mb-0">Rp {{ number_format($part->price, 0, ',', '.') }}</h3>
                                    <small class="text-muted">Harga belum termasuk ongkos kirim</small>
                                </div>
                            @endif

                            <!-- Quantity Selector -->
                            <div class="mb-4">
                                <label for="quantity" class="form-label fw-semibold">Jumlah:</label>
                                <div class="input-group" style="width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                                    <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="10" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>

                            <!-- Buy Now Button -->
                            <a href="{{ route('part.checkout', $part) }}?quantity={{ request('quantity', 1) }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-shopping-cart me-2"></i>Beli Sekarang
                            </a>

                            <!-- Back Button -->
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card mt-4 shadow">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="fas fa-info-circle me-2"></i>Informasi Pengiriman
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-truck text-primary me-2"></i>
                            Pengiriman menggunakan layanan kurir terpercaya
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-primary me-2"></i>
                            Estimasi pengiriman 2-5 hari kerja
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            Garansi produk asli dan berkualitas
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-credit-card text-primary me-2"></i>
                            Pembayaran aman via QRIS, Tripay, atau Midtrans
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    let currentValue = parseInt(quantityInput.value);
    let newValue = currentValue + delta;

    // Ensure value is between 1 and 10
    newValue = Math.max(1, Math.min(10, newValue));

    quantityInput.value = newValue;

    // Update buy now link
    const buyNowLink = document.querySelector('a[href*="checkout"]');
    const baseUrl = buyNowLink.href.split('?')[0];
    buyNowLink.href = baseUrl + '?quantity=' + newValue;
}
</script>
@endpush
