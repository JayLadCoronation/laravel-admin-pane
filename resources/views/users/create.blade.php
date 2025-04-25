@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add User</h2>
    <form method="POST" enctype="multipart/form-data" action="{{ route('users.store') }}">
        @csrf
        <div class="mb-2">
            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
        </div>
        <div class="mb-2">
            <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
        </div>
        <div class="mb-2">
            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
        </div>
        <div class="mb-2">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-2">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-2">
            <select name="gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="mb-2">
            <input type="file" name="profile_pic" class="form-control">
        </div>
        <div class="mb-2">
            <select name="country_id" id="country-dropdown" class="form-control" required>
                <option value="">Select Country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <select name="state_id" id="state-dropdown" class="form-control" required>
                <option value="">Select State</option>
            </select>
        </div>

        <div class="mb-2">
            <select name="city_id" id="city-dropdown" class="form-control" required>
                <option value="">Select City</option>
            </select>
        </div>

        <!-- TODO: AJAX for state and city dropdowns -->
        <div class="mb-2">
            <input type="text" name="state_id" class="form-control" placeholder="State ID (temp)">
        </div>
        <div class="mb-2">
            <input type="text" name="city_id" class="form-control" placeholder="City ID (temp)">
        </div>
        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#country-dropdown').on('change', function() {
        var country_id = this.value;
        $("#state-dropdown").html('');
        $("#city-dropdown").html('<option value="">Select City</option>');
        
        $.ajax({
            url: "/get-states/" + country_id,
            type: "GET",
            success: function(states) {
                $('#state-dropdown').html('<option value="">Select State</option>'); 
                $.each(states, function(key, state) {
                    $("#state-dropdown").append('<option value="'+state.id+'">'+state.name+'</option>');
                });
            }
        });
    });

    $('#state-dropdown').on('change', function() {
        var state_id = this.value;
        $("#city-dropdown").html('');
        
        $.ajax({
            url: "/get-cities/" + state_id,
            type: "GET",
            success: function(cities) {
                $('#city-dropdown').html('<option value="">Select City</option>'); 
                $.each(cities, function(key, city) {
                    $("#city-dropdown").append('<option value="'+city.id+'">'+city.name+'</option>');
                });
            }
        });
    });
</script>
@endpush
