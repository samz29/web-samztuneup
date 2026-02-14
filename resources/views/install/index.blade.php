<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer - SamzTune-Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">🚀 Installer SamzTune-Up</h3>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('/install') }}">
                            @csrf

                            <h5 class="mb-3">📊 Konfigurasi Database</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Database Host</label>
                                    <input type="text" name="db_host" class="form-control" value="{{ old('db_host', 'localhost') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Database Name</label>
                                    <input type="text" name="db_database" class="form-control" value="{{ old('db_database', 'samztune_up') }}" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Database Username</label>
                                    <input type="text" name="db_username" class="form-control" value="{{ old('db_username', 'root') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Database Password</label>
                                    <input type="password" name="db_password" class="form-control" value="{{ old('db_password') }}">
                                </div>
                            </div>

                            <h5 class="mb-3">⚙️ Konfigurasi Aplikasi</h5>
                            <div class="mb-3">
                                <label class="form-label">Nama Aplikasi</label>
                                <input type="text" name="app_name" class="form-control" value="{{ old('app_name', 'SamzTune-Up') }}" required>
                            </div>

                            <h5 class="mb-3">👤 Akun Admin</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email Admin</label>
                                    <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password Admin</label>
                                    <input type="password" name="admin_password" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    🚀 Install Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
