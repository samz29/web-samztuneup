@extends('admin.layout')

@section('title', 'Edit Promo Slider')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Promo Slider</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.promo-sliders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Sliders
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.promo-sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $slider->title) }}" required>
                        <small class="form-text text-muted">Display title for the slider</small>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $slider->description) }}</textarea>
                        <small class="form-text text-muted">Optional description text</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Slider Image</label>
                        @if($slider->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" class="img-thumbnail" style="max-width: 300px;">
                                <small class="form-text text-muted">Current image: {{ $slider->image }}</small>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*">
                        <small class="form-text text-muted">Leave empty to keep current image. Recommended size: 1920x600px</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="link_url">Link URL</label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror"
                               id="link_url" name="link_url" value="{{ old('link_url', $slider->link_url) }}"
                               placeholder="https://example.com or /internal-route">
                        <small class="form-text text-muted">Optional link when slider is clicked</small>
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="link_text">Link Text</label>
                        <input type="text" class="form-control @error('link_text') is-invalid @enderror"
                               id="link_text" name="link_text" value="{{ old('link_text', $slider->link_text) }}"
                               placeholder="Learn More">
                        <small class="form-text text-muted">Text for the call-to-action button</small>
                        @error('link_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $slider->sort_order) }}" min="0">
                        <small class="form-text text-muted">Lower numbers appear first in the slider</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active Slider</label>
                        </div>
                        <small class="form-text text-muted">Inactive sliders won't appear on the website</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update Slider
                    </button>
                    <a href="{{ route('admin.promo-sliders.index') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <div class="float-right">
                        <form action="{{ route('admin.promo-sliders.destroy', $slider) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this slider?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash mr-1"></i> Delete Slider
                            </button>
                        </form>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
