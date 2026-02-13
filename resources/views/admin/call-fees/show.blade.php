@extends('admin.layout')

@section('title', 'Call Fee Details - ' . $callFee->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Call Fee Details</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.call-fees.edit', $callFee) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.call-fees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>ID:</th>
                        <td>{{ $callFee->id }}</td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td><strong>{{ $callFee->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Minimum Distance:</th>
                        <td>{{ $callFee->min_distance }} km</td>
                    </tr>
                    <tr>
                        <th>Maximum Distance:</th>
                        <td>{{ $callFee->max_distance }} km</td>
                    </tr>
                    <tr>
                        <th>Distance Range:</th>
                        <td><strong>{{ $callFee->min_distance }} - {{ $callFee->max_distance }} km</strong></td>
                    </tr>
                    <tr>
                        <th>Fee:</th>
                        <td><strong class="text-success">Rp {{ number_format($callFee->fee, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge badge-{{ $callFee->active ? 'success' : 'secondary' }}">
                                {{ $callFee->active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $callFee->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $callFee->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.call-fees.update', $callFee) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="active">Status</label>
                        <select name="active" id="active" class="form-control">
                            <option value="1" {{ $callFee->active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$callFee->active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Update Status
                    </button>
                </form>

                <a href="{{ route('admin.call-fees.edit', $callFee) }}" class="btn btn-warning btn-block">
                    <i class="fas fa-edit mr-1"></i> Edit Call Fee
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
