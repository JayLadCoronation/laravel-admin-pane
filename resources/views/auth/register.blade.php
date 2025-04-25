<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Register</h2>
    <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="first_name" class="form-control" placeholder="First Name" required></div>
            <div class="col-md-4 mb-3"><input type="text" name="middle_name" class="form-control" placeholder="Middle Name"></div>
            <div class="col-md-4 mb-3"><input type="text" name="last_name" class="form-control" placeholder="Last Name" required></div>
        </div>
        <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
        <div class="mb-3"><input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required></div>
        <div class="mb-3">
            <select name="gender" class="form-control" required>
                <option value="">-- Gender --</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="mb-3"><input type="file" name="profile_pic" class="form-control"></div>
        <div class="row">
            <div class="col-md-4 mb-3"><input type="number" name="country_id" class="form-control" placeholder="Country ID" required></div>
            <div class="col-md-4 mb-3"><input type="number" name="state_id" class="form-control" placeholder="State ID" required></div>
            <div class="col-md-4 mb-3"><input type="number" name="city_id" class="form-control" placeholder="City ID" required></div>
        </div>
        <div class="mb-3">
            <select name="role_id" class="form-control" required>
                <option value="">-- Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Register</button>
    </form>
</div>
</body>
</html>
