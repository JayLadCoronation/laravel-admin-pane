@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Role</h2>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-2">
            <label>Role Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
