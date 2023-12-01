<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-stone-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" sizes="180x180" href="/images/icon180.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/images/icon192.png">
    <title>assistタイムライン</title>

</head>

<body>
    <div id="app"></div>
</body>

</html>
