@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Role</h2>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-2">
            <label>Role Name</label>
            <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
        </div>

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
