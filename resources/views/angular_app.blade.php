<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Angular App Title</title>
    <link rel="stylesheet" href="{{ asset('public/print-class/style.css') }}">
</head>
<body>
<app-root></app-root>
<script src="{{ asset('public/print-class/runtime.js') }}" type="module"></script>
<script src="{{ asset('public/ng/polyfills.js') }}"></script>
<script src="{{ asset('public/print-class/main.js') }}" defer></script>
</body>
</html>
