<!DOCTYPE html>
<html>
<head>
    <title>Your Admin Credentials</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            padding: 20px;
            border-radius: 8px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #FC8019;
        }
        p {
            line-height: 1.5;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
        .button {
            background-color: #FC8019;
            color: white !important;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, {{ $admin['first_name'] }} {{ $admin['last_name'] }}!</h1>

        @if ($admin['role_id'] == 1)
            <p>Congratulations! Your admin account has been created successfully.</p>
        @elseif ($admin['role_id'] == 2)
            <p>Great news! Your staff account has been created successfully.</p>
        @endif

        <p><strong>Email:</strong> {{ $admin['email'] }}</p>
        <p><strong>Password:</strong> {{ $password }}</p>
        
        <p>To access your account and get started, click the button below:</p>
        <p>
            <a class="button" href="http://localhost:3000/admincms">Access AdminCMS</a>
        </p>

        <p>If you have any questions or need assistance, feel free to reach out. We’re here to support you!</p>

        <div class="footer">
            <p>Best regards,</p>
            <p>The Admin Team</p>
        </div>
    </div>
</body>
</html>
