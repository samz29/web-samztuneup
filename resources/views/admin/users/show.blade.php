@extends('admin.layout')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Details</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>ID:</th>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td><strong>{{ $user->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $user->phone ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email Verified:</th>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i> Verified on {{ $user->email_verified_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i> Not Verified
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th>Created:</th>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Last Login:</th>
                                <td>-</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-user mr-1"></i> Active
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="mb-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        <i class="fas fa-trash mr-1"></i> Delete User
                    </button>
                </form>
                @endif

                @if(!$user->email_verified_at)
                <form action="{{ route('admin.users.verify-email', $user) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-check mr-1"></i> Mark Email as Verified
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit mr-1"></i> Edit User
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
