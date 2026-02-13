@extends('admin.layout')

@section('title', 'Web Menus')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Web Menus</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.web-menus.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add New Menu Item
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($menus->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-list fa-3x text-muted mb-3"></i>
                        <h4>No Menu Items Found</h4>
                        <p class="text-muted">Start by creating your first menu item.</p>
                        <a href="{{ route('admin.web-menus.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create First Menu Item
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>URL</th>
                                    <th>Location</th>
                                    <th>Parent</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menus as $menu)
                                <tr>
                                    <td>
                                        @if($menu->parent_id)
                                            <span class="text-muted" style="margin-left: {{ $menu->parent ? '20px' : '0' }};">
                                                <i class="fas fa-arrow-right mr-2"></i>
                                            </span>
                                        @endif
                                        {{ $menu->title }}
                                        @if($menu->icon)
                                            <i class="{{ $menu->icon }} ml-2 text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->url)
                                            <a href="{{ $menu->url }}" target="_blank" class="text-primary">
                                                {{ $menu->url }} <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $menu->location === 'header' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($menu->location) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($menu->parent)
                                            {{ $menu->parent->title }}
                                        @else
                                            <span class="text-muted">Root</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->sort_order }}</td>
                                    <td>
                                        <span class="badge badge-{{ $menu->is_active ? 'success' : 'danger' }}">
                                            {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.web-menus.edit', $menu) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.web-menus.destroy', $menu) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this menu item?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Menu Preview -->
@if($menus->isNotEmpty())
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Menu Preview</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Header Menu</h5>
                        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
                            <ul class="navbar-nav">
                                @foreach($menus->where('location', 'header')->whereNull('parent_id')->where('is_active', true)->sortBy('sort_order') as $headerMenu)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                        {{ $headerMenu->title }}
                                    </a>
                                    @if($headerMenu->children->count() > 0)
                                    <div class="dropdown-menu">
                                        @foreach($headerMenu->children->where('is_active', true)->sortBy('sort_order') as $child)
                                        <a class="dropdown-item" href="{{ $child->url }}">{{ $child->title }}</a>
                                        @endforeach
                                    </div>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                    <div class="col-md-6">
                        <h5>Footer Menu</h5>
                        <ul class="list-inline">
                            @foreach($menus->where('location', 'footer')->where('is_active', true)->sortBy('sort_order') as $footerMenu)
                            <li class="list-inline-item">
                                <a href="{{ $footerMenu->url }}" class="text-decoration-none">{{ $footerMenu->title }}</a>
                                @if(!$loop->last)
                                <span class="text-muted">|</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
