<!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Your App</title>

    </head>
    <body>
        <!-- the order of the scripts is important -->
        <div id="app"></div>
        <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
        <script>
            window.__INITIAL_TRANSLATIONS__ = @json(app('cms-translations')->getTranslations(app()->getLocale()));
        </script>
        <script src="{{ asset('vendor/charlesg-cms/charlesg-cms.umd.js') }}"></script>
        <link href="{{ asset('vendor/charlesg-cms/style.css') }}" rel="stylesheet">
    </body>
</html>
