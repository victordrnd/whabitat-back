<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Tasksman</title>
        <!-- Styles -->
        <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="example"></div>

        <script  src="{{ asset('assets/js/app.js') }}"></script>
    </body>
    </html>