<!DOCTYPE html>
<html>
<head>
    <title>OTP Quick Bites</title>
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
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #FC8019;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Hello,</p>
        <p>Your one-time password (OTP) for the Quick Bites app is:</p>
        <p class="otp">{{ $otp }}</p>
        <p>This OTP is valid for the next 10 minutes, so please use it soon!</p>
        <p>If you did not request this OTP, you can safely ignore this message. For your security, we recommend checking your account activity to ensure everything is in order.</p>
        <p>Thank you for using Quick Bites! If you have any questions, feel free to reach out to our support team.</p>
        <div class="footer">
            <p>Stay safe,</p>
            <p>The Quick Bites Team</p>
        </div>
    </div>
</body>
</html>
