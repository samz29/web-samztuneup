@extends('admin.layout')

@section('title', 'Tambah Foto Galeri')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Foto Galeri</h3>
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary btn-sm float-end">Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="image" class="form-label">Foto <span class="text-danger">*</span></label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" required accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="caption" class="form-label">Caption</label>
                <input type="text" name="caption" id="caption" class="form-control @error('caption') is-invalid @enderror" maxlength="100">
                @error('caption')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="sort_order" class="form-label">Urutan</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="0" min="0">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                <label class="form-check-label" for="is_active">Tampilkan</label>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan</button>
        </form>
        @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
