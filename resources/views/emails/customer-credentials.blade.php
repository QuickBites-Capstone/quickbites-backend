<!DOCTYPE html>
<html>
<head>
    <title>Your Account Credentials</title>
</head>
<body>
    <h1>Welcome, {{ $customer['first_name'] }} {{ $customer['last_name'] }}</h1>
    <p>Your account has been created successfully.</p>
    <p><strong>Email:</strong> {{ $customer['email'] }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>
</body>
</html>