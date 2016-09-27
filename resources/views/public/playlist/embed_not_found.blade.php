<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel='shortcut icon' href='{{ asset('img/favicon-32x32.png') }}' type='image/x-icon'/>
    <link href='https://fonts.googleapis.com/css?family=Signika+Negative:400,300,600,700' rel='stylesheet'
          type='text/css'>
    <link href="{{ asset('embed/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('embed/css/slider.min.css') }}" rel="stylesheet">
    <title>{{ trans('playlist.not_found') }}</title>
</head>
<body>
<div class="table">
    <header class="clearfix">
        <a target="_blank" href="{{ url('/') }}" class="logo">
            <img src="{{ asset('img/logo_playligo_md.png') }}" alt="Playligo">
        </a>
        <a target="_blank" href="{{ url('search') }}" class="btn btn-success">Visualize Your Travel</a>
    </header>
    <main class="container">
        <div class="content">
            <div class="title">
                {{ trans('playlist.not_found') }}
            </div>
        </div>
    </main>
</div>
</body>
</html>