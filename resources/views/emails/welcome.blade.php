<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $appName }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        .welcome-message {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .email-content {
            font-size: 16px;
            color: #555555;
            margin-bottom: 30px;
        }
        .cta-button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .features {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .features h3 {
            margin-top: 0;
            color: #333333;
            font-size: 20px;
        }
        .feature-item {
            margin: 15px 0;
            padding-left: 30px;
            position: relative;
            color: #555555;
        }
        .feature-item:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            font-size: 14px;
            color: #888888;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 30px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            .email-header {
                padding: 30px 20px;
            }
            .email-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎉 Welcome to {{ $appName }}!</h1>
        </div>
        
        <div class="email-body">
            <div class="welcome-message">
                Hello {{ $userName }},
            </div>
            
            <div class="email-content">
                <p>We're thrilled to have you join our community! Your account has been successfully created and you're all set to get started.</p>
                
                <p>Your account details:</p>
                <ul style="color: #555555; margin: 15px 0;">
                    <li><strong>Email:</strong> {{ $userEmail }}</li>
                    <li><strong>Name:</strong> {{ $userName }}</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ $appUrl }}" class="cta-button">Get Started</a>
            </div>

            <div class="features">
                <h3>What you can do:</h3>
                <div class="feature-item">Explore our blog and discover amazing content</div>
                <div class="feature-item">Create and share your own blog posts</div>
                <div class="feature-item">Connect with other members of our community</div>
                <div class="feature-item">Stay updated with the latest news and updates</div>
            </div>

            <div class="divider"></div>

            <div class="email-content">
                <p style="font-size: 14px; color: #888888;">
                    If you have any questions or need assistance, feel free to reach out to our support team. We're here to help!
                </p>
            </div>
        </div>
        
        <div class="email-footer">
            <p style="margin: 0 0 10px 0;">
                <strong>{{ $appName }}</strong>
            </p>
            <p style="margin: 0 0 10px 0;">
                <a href="{{ $appUrl }}">{{ $appUrl }}</a>
            </p>
            <p style="margin: 0; font-size: 12px;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
