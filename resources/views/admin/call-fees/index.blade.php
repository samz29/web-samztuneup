@extends('admin.layout')

@section('title', 'Call Fees Management')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Call Fees</h3>
        <div class="card-tools">
            <a href="{{ route('admin.call-fees.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Add Call Fee
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Distance Range</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($callFees as $callFee)
                    <tr>
                        <td>{{ $callFee->id }}</td>
                        <td>
                            <strong>{{ $callFee->name }}</strong>
                        </td>
                        <td>
                            {{ $callFee->min_distance }} - {{ $callFee->max_distance }} km
                        </td>
                        <td>
                            <strong>Rp {{ number_format($callFee->fee, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-{{ $callFee->active ? 'success' : 'secondary' }}">
                                {{ $callFee->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.call-fees.show', $callFee) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.call-fees.edit', $callFee) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.call-fees.destroy', $callFee) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this call fee?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            No call fees found. <a href="{{ route('admin.call-fees.create') }}">Create one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $callFees->links() }}
    </div>
</div>
@endsection
