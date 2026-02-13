@extends('admin.layout')

@section('title', 'Add New App Setting')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New App Setting</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Settings
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.app-settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="key">Setting Key *</label>
                        <input type="text" class="form-control @error('key') is-invalid @enderror"
                               id="key" name="key" value="{{ old('key') }}" required>
                        <small class="form-text text-muted">Unique identifier for this setting (e.g., app_name, contact_email)</small>
                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="value">Default Value</label>
                        <input type="text" class="form-control @error('value') is-invalid @enderror"
                               id="value" name="value" value="{{ old('value') }}">
                        <small class="form-text text-muted">Default value for this setting</small>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Setting Type *</label>
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="string" {{ old('type') == 'string' ? 'selected' : '' }}>String/Text</option>
                            <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean (Yes/No)</option>
                            <option value="integer" {{ old('type') == 'integer' ? 'selected' : '' }}>Integer/Number</option>
                            <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>File/Image</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="file-upload-group" style="display: none;">
                        <label for="file">Upload File</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept="image/*">
                        <small class="form-text text-muted">Upload an image file for this setting</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="group">Setting Group</label>
                        <input type="text" class="form-control @error('group') is-invalid @enderror"
                               id="group" name="group" value="{{ old('group', 'general') }}"
                               placeholder="e.g., general, branding, contact">
                        <small class="form-text text-muted">Group this setting belongs to (for organization)</small>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        <small class="form-text text-muted">Optional description of what this setting does</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active Setting</label>
                        </div>
                        <small class="form-text text-muted">Inactive settings won't be used by the application</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Setting
                    </button>
                    <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
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
        if ($(this).val() === 'file') {
            $('#file-upload-group').show();
        } else {
            $('#file-upload-group').hide();
        }
    });

    // Trigger change on page load if file type is selected
    $('#type').trigger('change');
});
</script>
@endpush
