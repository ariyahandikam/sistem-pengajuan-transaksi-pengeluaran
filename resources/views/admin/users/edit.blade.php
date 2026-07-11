@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit User</h4>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
            <small class="text-muted">Leave blank to keep current password.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role_id" class="form-control">
                <option value="">-</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ $user->role_id == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ ($user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ ($user->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
