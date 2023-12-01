<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no">
    <script> window.timelineCsrf = "{{ csrf_token() }}"; </script>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/login/reset.css') }}" rel="stylesheet">
    <link href="{{ mix('css/login/style.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" sizes="180x180" href="/images/icon180.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/images/icon192.png">
    <title>assistタイムライン ログイン</title>
    <script src="https://kit.fontawesome.com/f513e0a7e1.js" crossorigin="anonymous"></script>
</head>

<body>
    <div id="app"></div>
</body>

</html>
