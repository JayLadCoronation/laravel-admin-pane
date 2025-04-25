@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Users <a href="{{ route('users.create') }}" class="btn btn-sm btn-success float-end">Add User</a></h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Gender</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->gender) }}</td>
                <td>{{ $user->status ? 'Active' : 'Inactive' }}</td>
                <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this user?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
