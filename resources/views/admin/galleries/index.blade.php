@extends('admin.layout')

@section('title', 'Galeri Foto')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Galeri Foto</h3>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Foto
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($galleries as $gallery)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset('storage/' . $gallery->image) }}" class="card-img-top" style="object-fit:cover; height:180px; border-radius:12px 12px 0 0;">
                        <div class="card-body p-2">
                            <div class="small text-muted mb-1">{{ $gallery->caption }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus foto ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">Belum ada foto galeri.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
