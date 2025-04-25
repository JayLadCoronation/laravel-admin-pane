@extends('layouts.app')

@section('content')
        <div class="p-4">
            <h2>Welcome, {{ auth()->user()->first_name }}</h2>
        </div>
@endsection
