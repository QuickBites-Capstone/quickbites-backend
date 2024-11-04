<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Quick Bites!</title>
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
        <h1>Welcome to Quick Bites!</h1>
        <h2>Hi, {{ $customer['first_name'] }} {{ $customer['last_name'] }}!</h2>
        <p>We’re excited to have you join our community! Get ready to explore our delicious menu and effortlessly pre-order your favorite meals.</p>
        <p>To get started, check out our menu and find something you love!</p>
        <p>
            <a class="button" href="https://quickbites.passafund.com">Explore Quick Bites</a>
        </p>
        <p>If you have any questions or need assistance, don’t hesitate to reach out to our support team. We’re here to help!</p>
        <div class="footer">
            <p>Happy eating,</p>
            <p>The Quick Bites Team</p>
        </div>
    </div>
</body>
</html>
