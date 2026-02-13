@extends('admin.layout')

@section('title', 'Edit Service Rate')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Service Rate</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.service-rates.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Rates
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.service-rates.update', $serviceRate) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="service_name">Service Name *</label>
                        @php
                            $coreServices = ['remap_ecu', 'custom_tune', 'dyno_tune', 'full_package'];
                            $isCoreService = in_array($serviceRate->service_name, $coreServices);
                        @endphp
                        <input type="text" class="form-control @error('service_name') is-invalid @enderror"
                               id="service_name" name="service_name" value="{{ old('service_name', $serviceRate->service_name) }}"
                               {{ $isCoreService ? 'readonly' : 'required' }}>
                        @if($isCoreService)
                            <small class="form-text text-warning">⚠️ Core service name cannot be changed</small>
                        @else
                            <small class="form-text text-muted">Name of the service (e.g., Oil Change, Tire Replacement)</small>
                        @endif
                        @error('service_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $serviceRate->description) }}</textarea>
                        <small class="form-text text-muted">Optional description of the service</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="base_price">Base Price *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                                           id="base_price" name="base_price" value="{{ old('base_price', intval($serviceRate->base_price)) }}"
                                           min="0" step="0.01" required>
                                </div>
                                <small class="form-text text-muted">Base price in Rupiah</small>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="additional_fee">Additional Fee</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control @error('additional_fee') is-invalid @enderror"
                                           id="additional_fee" name="additional_fee" value="{{ old('additional_fee', intval($serviceRate->additional_fee)) }}"
                                           min="0" step="0.01">
                                </div>
                                <small class="form-text text-muted">Additional fee for extra services</small>
                                @error('additional_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit">
                            <option value="per_service" {{ old('unit', $serviceRate->unit) == 'per_service' ? 'selected' : '' }}>Per Service</option>
                            <option value="per_hour" {{ old('unit', $serviceRate->unit) == 'per_hour' ? 'selected' : '' }}>Per Hour</option>
                            <option value="per_item" {{ old('unit', $serviceRate->unit) == 'per_item' ? 'selected' : '' }}>Per Item</option>
                            <option value="per_km" {{ old('unit', $serviceRate->unit) == 'per_km' ? 'selected' : '' }}>Per Kilometer</option>
                        </select>
                        <small class="form-text text-muted">How the service is charged</small>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $serviceRate->sort_order) }}">
                        <small class="form-text text-muted">Order for displaying services (lower numbers appear first)</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $serviceRate->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active Service Rate</label>
                        </div>
                        <small class="form-text text-muted">Inactive rates won't be displayed to customers</small>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Service Rate
                    </button>
                    <a href="{{ route('admin.service-rates.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <div class="float-right">
                        @if(!$isCoreService)
                        <form action="{{ route('admin.service-rates.destroy', $serviceRate) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this service rate?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash mr-1"></i> Delete Rate
                            </button>
                        </form>
                        @else
                        <span class="text-muted small">⚠️ Core service cannot be deleted</span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
