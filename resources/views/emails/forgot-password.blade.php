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
        <p>We received a request to reset your Quick Bites password. To continue, please use the one-time password (OTP) below:</p>
        <p class="otp">{{ $otp }}</p>
        <p>This OTP is valid for the next 10 minutes, so be sure to enter it promptly to reset your password.</p>
        <p>If you didn’t request a password reset, you can ignore this message. For added security, we recommend reviewing your account activity.</p>
        <p>Thank you for being part of Quick Bites! If you need assistance, our support team is here to help.</p>
        <div class="footer">
            <p>Stay safe,</p>
            <p>The Quick Bites Team</p>
        </div>
    </div>
</body>

</html>
