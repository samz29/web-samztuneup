@extends('admin.layout')

@section('title', 'Create New Call Fee')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Call Fee</h3>
        <div class="card-tools">
            <a href="{{ route('admin.call-fees.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>
    <form action="{{ route('admin.call-fees.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Within City, Long Distance" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="min_distance">Minimum Distance (km) *</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('min_distance') is-invalid @enderror" id="min_distance" name="min_distance" value="{{ old('min_distance') }}" required>
                        @error('min_distance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="max_distance">Maximum Distance (km) *</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('max_distance') is-invalid @enderror" id="max_distance" name="max_distance" value="{{ old('max_distance') }}" required>
                        @error('max_distance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="fee">Fee (Rp) *</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" value="{{ old('fee') }}" required>
                        @error('fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                    <label for="active" class="custom-control-label">
                        Active
                    </label>
                </div>
                <small class="form-text text-muted">Check this box to make this call fee active and available for use</small>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Create Call Fee
            </button>
            <a href="{{ route('admin.call-fees.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
