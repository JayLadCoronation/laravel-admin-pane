@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit User</h2>

    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        @include('users.form', ['user' => $user])
        
        <button type="submit" class="btn btn-primary">Update User</button>
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