<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installasi Berhasil - SamzTune-Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-success">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0">✅ Installasi Berhasil!</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>

                        <h4 class="mb-3">Selamat! SamzTune-Up telah berhasil diinstall</h4>

                        <p class="mb-4">
                            Aplikasi Anda sekarang siap digunakan. Berikut adalah langkah selanjutnya:
                        </p>

                        <div class="alert alert-info text-start">
                            <h6>📋 Yang telah dilakukan:</h6>
                            <ul class="mb-0">
                                <li>✅ Konfigurasi database berhasil</li>
                                <li>✅ Tabel database telah dibuat</li>
                                <li>✅ Data awal telah dimasukkan</li>
                                <li>✅ Akun admin telah dibuat</li>
                                <li>✅ Konfigurasi aplikasi telah diset</li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ url('/') }}" class="btn btn-primary btn-lg w-100 mb-2">
                                    🏠 Ke Halaman Utama
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/admin') }}" class="btn btn-secondary btn-lg w-100 mb-2">
                                    👤 Login Admin
                                </a>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <strong>⚠️ Penting:</strong> Hapus folder <code>install</code> dari server untuk alasan keamanan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
