<!DOCTYPE html>
<html>
<head>
    <title>Your Admin Credentials</title>
</head>
<body>
    <h1>Welcome, {{ $admin['first_name'] }} {{ $admin['last_name'] }}</h1>
    <p>Your admin account has been created successfully.</p>
    <p><strong>Email:</strong> {{ $admin['email'] }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
</body>
</html>