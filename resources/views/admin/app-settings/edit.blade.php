@extends('admin.layout')

@section('title', 'Edit App Setting')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit App Setting: {{ $setting->key }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Settings
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.app-settings.update', $setting) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="key">Setting Key *</label>
                        <input type="text" class="form-control @error('key') is-invalid @enderror"
                               id="key" name="key" value="{{ old('key', $setting->key) }}" required>
                        <small class="form-text text-muted">Unique identifier for this setting</small>
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="value">Value</label>
                        @if($setting->type === 'boolean')
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="value" value="0">
                                <input type="checkbox" class="custom-control-input"
                                       id="value" name="value" value="1" {{ old('value', $setting->value) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="value">Enabled</label>
                            </div>
                        @elseif($setting->type === 'file')
                            @if($setting->value)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/app/public/' . $setting->value) }}" alt="{{ $setting->key }}" class="img-thumbnail" style="max-width: 200px;">
                                    <small class="form-text text-muted">Current file: {{ $setting->value }}</small>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep current file</small>
                            <input type="hidden" name="value" value="{{ $setting->value }}">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @else
                            <input type="text" class="form-control @error('value') is-invalid @enderror"
                                   id="value" name="value" value="{{ old('value', $setting->value) }}">
                        @endif
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Setting Type *</label>
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="string" {{ old('type', $setting->type) == 'string' ? 'selected' : '' }}>String/Text</option>
                            <option value="boolean" {{ old('type', $setting->type) == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                            <option value="integer" {{ old('type', $setting->type) == 'integer' ? 'selected' : '' }}>Integer/Number</option>
                            <option value="file" {{ old('type', $setting->type) == 'file' ? 'selected' : '' }}>File/Image</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="group">Setting Group</label>
                        <input type="text" class="form-control @error('group') is-invalid @enderror"
                               id="group" name="group" value="{{ old('group', $setting->group) }}"
                               placeholder="e.g., general, branding, contact">
                        <small class="form-text text-muted">Group this setting belongs to</small>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $setting->description) }}</textarea>
                        <small class="form-text text-muted">Description of what this setting does</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active Setting</label>
                        </div>
                        <small class="form-text text-muted">Inactive settings won't be used by the application</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Setting
                    </button>
                    <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <div class="float-right">
                        <form action="{{ route('admin.app-settings.destroy', $setting) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this setting?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash mr-1"></i> Delete Setting
                            </button>
                        </form>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#type').change(function() {
        // Handle type changes if needed
    });
});
</script>
@endpush
