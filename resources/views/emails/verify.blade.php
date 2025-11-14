<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('emails.verify_subject', ['app' => $appName]) }}</title>
</head>
<body>
    <h1>{{ __('emails.verify_greeting', ['name' => $userName]) }}</h1>
    <p>{{ __('emails.verify_message', ['app' => $appName]) }}</p>
    <p>
        <a href="{{ $verificationUrl }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            {{ __('emails.verify_button') }}
        </a>
    </p>
    <p>{{ __('emails.verify_alternative') }}</p>
    <p><small>{{ $verificationUrl }}</small></p>
</body>
</html>

