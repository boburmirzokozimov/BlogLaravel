<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.verify_subject', ['app' => $appName]) }}</title>
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

        .greeting {
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
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            word-break: break-word;
        }

        .cta-button:hover {
            opacity: 0.9;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .alternative-text {
            font-size: 14px;
            color: #888888;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .url-fallback {
            font-size: 12px;
            color: #999999;
            word-break: break-all;
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-family: monospace;
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

            .cta-button {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>✉️ {{ __('emails.verify_subject', ['app' => $appName]) }}</h1>
    </div>

    <div class="email-body">
        <div class="greeting">
            {{ __('emails.verify_greeting', ['name' => $userName]) }}
        </div>

        <div class="email-content">
            <p>{{ __('emails.verify_message', ['app' => $appName]) }}</p>
        </div>

        <div class="button-container">
            <a href="{{ $verificationUrl }}" class="cta-button" style="color: #ffffff !important;">
                {{ __('emails.verify_button') }}
            </a>
        </div>

        <div class="alternative-text">
            <p>{{ __('emails.verify_alternative') }}</p>
        </div>

        <div class="url-fallback">
            <p style="font-size: 11px; color: #999999; margin: 0 0 8px;">
                If the button above doesn't work, copy and paste this link into your browser:
            </p>
            <p style="margin: 0; word-break: break-all; font-family: 'Courier New', monospace; font-size: 10px; color: #666666; line-height: 1.4;">
                {{ $verificationUrl }}
            </p>
        </div>
    </div>

    <div class="email-footer">
        <p style="margin: 0 0 10px 0;">
            <strong>{{ $appName }}</strong>
        </p>
        <p style="margin: 0; font-size: 12px;">
            This is an automated email. Please do not reply to this message.
        </p>
    </div>
</div>
</body>
</html>
