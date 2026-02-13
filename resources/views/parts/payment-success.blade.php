@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-success">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Pembayaran Berhasil!
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-success">Pesanan Anda telah diterima!</h5>
                        <p class="text-muted">Terima kasih telah berbelanja di SamzTune-Up</p>
                    </div>

                    <!-- Order Details -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-3">Detail Pesanan</h6>
                                    <div class="row text-start">
                                        <div class="col-sm-4"><strong>No. Pesanan:</strong></div>
                                        <div class="col-sm-8">{{ $order->order_code }}</div>

                                        <div class="col-sm-4"><strong>Produk:</strong></div>
                                        <div class="col-sm-8">{{ $order->part->name }}</div>

                                        <div class="col-sm-4"><strong>Jumlah:</strong></div>
                                        <div class="col-sm-8">{{ $order->quantity }} pcs</div>

                                        <div class="col-sm-4"><strong>Harga Satuan:</strong></div>
                                        <div class="col-sm-8">Rp {{ number_format($order->unit_price, 0, ',', '.') }}</div>

                                        <div class="col-sm-4"><strong>Ongkos Kirim:</strong></div>
                                        <div class="col-sm-8">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</div>

                                        <div class="col-sm-4"><strong>Total:</strong></div>
                                        <div class="col-sm-8 fw-bold text-primary">Rp {{ number_format(($order->total_price + $order->shipping_cost), 0, ',', '.') }}</div>

                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $order->status === 'paid' ? 'success' : 'warning' }}">
                                                {{ $order->status === 'paid' ? 'Sudah Dibayar' : 'Menunggu Pembayaran' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    @if($order->shipping_details)
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-8">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3">
                                            <i class="fas fa-truck me-2"></i>Informasi Pengiriman
                                        </h6>
                                        <div class="row text-start">
                                            <div class="col-sm-4"><strong>Kurir:</strong></div>
                                            <div class="col-sm-8">{{ $order->shipping_courier }} - {{ $order->shipping_service }}</div>

                                            <div class="col-sm-4"><strong>Penerima:</strong></div>
                                            <div class="col-sm-8">{{ $order->customer_name }}</div>

                                            <div class="col-sm-4"><strong>Alamat:</strong></div>
                                            <div class="col-sm-8">{{ $order->shipping_address }}, {{ $order->shipping_city }} {{ $order->shipping_postal_code }}</div>

                                            <div class="col-sm-4"><strong>No. Resi:</strong></div>
                                            <div class="col-sm-8">
                                                @if(isset($order->shipping_details['tracking_number']))
                                                    <span class="badge bg-info">{{ $order->shipping_details['tracking_number'] }}</span>
                                                @else
                                                    <span class="text-muted">Belum tersedia</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Kembali ke Beranda
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Cetak Invoice
                        </button>
                    </div>

                    <!-- Additional Information -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="fw-bold mb-2">Informasi Penting:</h6>
                        <ul class="text-start mb-0 small">
                            <li>Simpan nomor pesanan untuk tracking</li>
                            <li>Konfirmasi pembayaran akan dikirim via email</li>
                            <li>Pengiriman akan diproses dalam 1-2 hari kerja</li>
                            <li>Untuk pertanyaan, hubungi kami di admin@samztune-up.com</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .card-header, .mt-4 {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
