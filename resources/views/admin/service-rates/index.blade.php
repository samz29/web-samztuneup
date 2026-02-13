@extends('admin.layout')

@section('title', 'Service Rates Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Service Rates</h3>
        <div class="card-tools">
            <a href="{{ route('admin.service-rates.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add Service Rate
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Base Price</th>
                        <th>Unit</th>
                        <th>Additional Fee</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceRates as $rate)
                    <tr>
                        <td>{{ $rate->service_name }}</td>
                        <td>{{ Str::limit($rate->description, 50) }}</td>
                        <td>Rp {{ number_format($rate->base_price, 0, ',', '.') }}</td>
                        <td>{{ $rate->unit }}</td>
                        <td>Rp {{ number_format($rate->additional_fee, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $rate->is_active ? 'success' : 'secondary' }}">
                                {{ $rate->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $rate->sort_order }}</td>
                        <td>
                            <a href="{{ route('admin.service-rates.edit', $rate) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.service-rates.destroy', $rate) }}" method="POST" class="d-inline">
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
                        <td colspan="8" class="text-center text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            No service rates found. <a href="{{ route('admin.service-rates.create') }}">Create one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $serviceRates->links() }}
    </div>
</div>
@endsection
