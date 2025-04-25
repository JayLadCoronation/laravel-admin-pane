@php
    $isEdit = isset($user);
@endphp

<div class="mb-2">
    <label>First Name</label>
    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name ?? '') }}" required>
</div>

<div class="mb-2">
    <label>Last Name</label>
    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name ?? '') }}" required>
</div>

<div class="mb-2">
    <label>Middle Name</label>
    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $user->middle_name ?? '') }}">
</div>

<div class="mb-2">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
</div>

@if (!$isEdit)
<div class="mb-2">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
</div>
@endif

<div class="mb-2">
    <label>Gender</label>
    <select name="gender" class="form-control">
        <option value="male" {{ (old('gender', $user->gender ?? '') == 'male') ? 'selected' : '' }}>Male</option>
        <option value="female" {{ (old('gender', $user->gender ?? '') == 'female') ? 'selected' : '' }}>Female</option>
    </select>
</div>

<div class="mb-2">
    <label>Profile Picture</label><br>
    @if ($isEdit && $user->profile_pic)
        <img src="{{ asset('uploads/' . $user->profile_pic) }}" width="60" class="mb-2"><br>
    @endif
    <input type="file" name="profile_pic" class="form-control">
</div>

<div class="mb-2">
    <label>Role</label>
    <select name="role_id" class="form-control" required>
        @foreach ($roles as $role)
            <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id ?? '') == $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-2">
    <label>Country</label>
    <select name="country_id" id="country-dropdown" class="form-control" required>
        <option value="">Select Country</option>
        @foreach ($countries as $country)
            <option value="{{ $country->id }}" {{ (old('country_id', $user->country_id ?? '') == $country->id) ? 'selected' : '' }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-2">
    <label>State</label>
    <select name="state_id" id="state-dropdown" class="form-control" required>
        <option value="">Select State</option>
        @foreach ($states ?? [] as $state)
            <option value="{{ $state->id }}" {{ (old('state_id', $user->state_id ?? '') == $state->id) ? 'selected' : '' }}>{{ $state->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-2">
    <label>City</label>
    <select name="city_id" id="city-dropdown" class="form-control" required>
        <option value="">Select City</option>
        @foreach ($cities ?? [] as $city)
            <option value="{{ $city->id }}" {{ (old('city_id', $user->city_id ?? '') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
        @endforeach
    </select>
</div>

@push('scripts')

@endpush
