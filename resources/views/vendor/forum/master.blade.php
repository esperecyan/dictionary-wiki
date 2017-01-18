<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if (isset($thread))
            {{ $thread->title }} -
        @endif
        @if (isset($category))
            {{ $category->title }} -
        @endif
        {{ trans('forum::general.home_title') }}
    </title>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery.js') }}"></script>

    <!-- Bootstrap -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-theme.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/bootstrap.js') }}"></script>

    <link href="{{ asset('css/forum-master.css') }}" rel="stylesheet" />
</head>
<body>
    <div class="container">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </div>

    <script src="{{ asset('js/forum-master.js') }}"></script>

    @yield('footer')
</body>
</html>
