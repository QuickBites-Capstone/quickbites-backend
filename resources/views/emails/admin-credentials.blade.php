<!DOCTYPE html>
<html>
<head>
    <title>Your Admin Credentials</title>
</head>
<body>
    <h1>Welcome, {{ $admin['first_name'] }} {{ $admin['last_name'] }}</h1>
    
    @if ($admin['role_id'] == 1)
        <p>Your admin account has been created successfully.</p>
    @elseif ($admin['role_id'] == 2)
        <p>Your staff account has been created successfully.</p>
    @endif

    <p><strong>Email:</strong> {{ $admin['email'] }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
    <p>
        Go to <a href="http://localhost:3000/admincms">AdminCMS</a>
    </p>
</body>
</html>
