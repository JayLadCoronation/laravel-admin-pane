<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Login</h2>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email"></div>
        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password"></div>
        <button class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
