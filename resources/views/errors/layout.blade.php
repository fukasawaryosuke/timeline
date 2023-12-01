<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
    <script type="text/javascript">
        let isAndroid = false;
        let isiOS = false;
        
        if (navigator.userAgent.indexOf('Android') > 0) {
            isAndroid = true;
        }
        if (navigator.userAgent.indexOf('iPhone') > 0 || navigator.userAgent.indexOf('iPad') > 0) {
            isiOS = true;
        }
        if (isiOS || isAndroid) {
            location.href = "/login";
        }
    </script>
</head>

<body>
    <!-- ヘッダ -->
    <div class="fixed w-full z-50 h-12 bg-cyan-500 flex border-b">
        <div class="absolute font-bold text-xl text-gray-600 pt-3 pl-7 cursor-pointer">
            <h1 class="text-white">assistタイムライン</h1>
        </div>
    </div>

    <!-- コンテンツ -->
    <div class="h-screen pt-24 pb-12 flex justify-center bg-white">
        <div class="w-4/5 flex justify-center">
            <div class="px-6 py-12 flex flex-col justify-center">
                <img src="/images/ico-logo-academy.svg" class="w-24 self-center mb-8" />
                <div class="font-bold text-2xl text-gray-500 mb-8 text-center">@yield('message_title')</div>
                <pre class="text-gray-700 text-base whitespace-pre-wrap text-center mb-8">@yield('message')</pre>
                <a href="{{ config('app.mypage_e_url') }}" class="rounded-2xl px-3 py-1 bg-cyan-500 text-white self-center">マイページへ</a>
            </div>
        </div>
    </div>
</body>

</html>
