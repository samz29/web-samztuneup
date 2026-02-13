@extends('admin.layout')

@section('title', 'Create Service Rate')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Service Rate</h3>
        <div class="card-tools">
            <a href="{{ route('admin.service-rates.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.service-rates.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="service_name">Service Name *</label>
                        <input type="text" class="form-control @error('service_name') is-invalid @enderror"
                               id="service_name" name="service_name" value="{{ old('service_name') }}" required>
                        @error('service_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="base_price">Base Price (Rp) *</label>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                                       id="base_price" name="base_price" value="{{ old('base_price') }}" min="0" step="0.01" required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit">Unit *</label>
                                <select class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                    <option value="service" {{ old('unit') == 'service' ? 'selected' : '' }}>Per Service</option>
                                    <option value="hour" {{ old('unit') == 'hour' ? 'selected' : '' }}>Per Hour</option>
                                    <option value="km" {{ old('unit') == 'km' ? 'selected' : '' }}>Per KM</option>
                                    <option value="day" {{ old('unit') == 'day' ? 'selected' : '' }}>Per Day</option>
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="additional_fee">Additional Fee (Rp)</label>
                                <input type="number" class="form-control @error('additional_fee') is-invalid @enderror"
                                       id="additional_fee" name="additional_fee" value="{{ old('additional_fee', 0) }}" min="0" step="0.01">
                                @error('additional_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input @error('is_active') is-invalid @enderror"
                                   id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Create Service Rate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
