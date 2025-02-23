<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? 'Email' }}</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
    @include('emails.header')

    <div style="padding: 20px;">
        {{-- This variable will hold the main email content --}}
        {!! $content !!}
    </div>

    @include('emails.footer')
</body>

</html>
