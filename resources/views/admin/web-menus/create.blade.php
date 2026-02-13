@extends('admin.layout')

@section('title', 'Add New Menu Item')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Menu Item</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.web-menus.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Menus
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.web-menus.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Menu Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        <small class="form-text text-muted">Display name for the menu item</small>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url') }}"
                               placeholder="https://example.com or /internal-route">
                        <small class="form-text text-muted">Full URL or internal route (leave empty for dropdown parent)</small>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="location">Menu Location *</label>
                        <select class="form-control @error('location') is-invalid @enderror" id="location" name="location" required>
                            <option value="">Select Location</option>
                            <option value="header" {{ old('location') == 'header' ? 'selected' : '' }}>Header Menu</option>
                            <option value="footer" {{ old('location') == 'footer' ? 'selected' : '' }}>Footer Menu</option>
                        </select>
                        <small class="form-text text-muted">Where this menu item will appear</small>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="parent_id">Parent Menu Item</label>
                        <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">Root Level (No Parent)</option>
                            @foreach($availableParents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->title }} ({{ ucfirst($parent->location) }})
                            </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Select a parent to create submenu items</small>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                        <small class="form-text text-muted">Lower numbers appear first (0 = highest priority)</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon Class</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror"
                               id="icon" name="icon" value="{{ old('icon') }}"
                               placeholder="fas fa-home">
                        <small class="form-text text-muted">FontAwesome icon class (optional)</small>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="target">Link Target</label>
                        <select class="form-control @error('target') is-invalid @enderror" id="target" name="target">
                            <option value="_self" {{ old('target', '_self') == '_self' ? 'selected' : '' }}>Same Window</option>
                            <option value="_blank" {{ old('target') == '_blank' ? 'selected' : '' }}>New Window/Tab</option>
                        </select>
                        <small class="form-text text-muted">How the link should open</small>
                        @error('target')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active Menu Item</label>
                        </div>
                        <small class="form-text text-muted">Inactive items won't appear in the menu</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Create Menu Item
                    </button>
                    <a href="{{ route('admin.web-menus.index') }}" class="btn btn-secondary ml-2">
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
    $('#location').change(function() {
        updateParentOptions();
    });

    function updateParentOptions() {
        var location = $('#location').val();
        $('#parent_id option').each(function() {
            var optionLocation = $(this).text().match(/\((Header|Footer)\)/);
            if (optionLocation) {
                var parentLocation = optionLocation[1].toLowerCase();
                $(this).prop('disabled', parentLocation !== location);
            }
        });
        $('#parent_id').val(''); // Reset selection when location changes
    }

    // Initial update
    updateParentOptions();
});
</script>
@endpush
