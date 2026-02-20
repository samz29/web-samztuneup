@extends('admin.layout')

@section('title', 'Promo Sliders Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Promo Sliders</h3>
        <div class="card-tools">
            <a href="{{ route('admin.promo-sliders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add Promo Slider
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Button Text</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promoSliders as $slider)
                    <tr>
                        <td>
                            @if($slider->image)
                                <img src="{{ asset('storage/app/public/' . $slider->image) }}" alt="{{ $slider->title }}" class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                            @else
                                <span class="text-muted">No image</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($slider->title, 30) }}</td>
                        <td>{{ Str::limit($slider->subtitle, 30) }}</td>
                        <td>{{ $slider->button_text ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $slider->is_active ? 'success' : 'secondary' }}">
                                {{ $slider->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $slider->sort_order }}</td>
                        <td>
                            <a href="{{ route('admin.promo-sliders.edit', $slider) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.promo-sliders.destroy', $slider) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            No promo sliders found. <a href="{{ route('admin.promo-sliders.create') }}">Create one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $promoSliders->links() }}
    </div>
</div>
@endsection
