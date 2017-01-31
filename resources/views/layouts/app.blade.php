<?php
use Illuminate\Support\HtmlString;
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    @stack('metas')

    <title>@hasSection('title')@yield('title') | @endif{{ _('辞書まとめwiki') }} α版</title>

    <!-- Fonts -->
    <link href="{{ asset('css/font-awesome.css') }}" rel="stylesheet" />

    <!-- Styles -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    @stack('styles')
</head>
<body id="app-layout">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                   {{  _('辞書まとめwiki') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/dictionaries') }}">{{ _('辞書一覧') }}</a></li>
                    <li><a href="{{ url('/users') }}">{{ _('ユーザー一覧') }}</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">{{ _('ログイン') }}</a></li>
                        <li><a href="{{ url('/register') }}">{{ _('ユーザー登録') }}</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <bdi>{{ Auth::user()->name }}</bdi> <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/dictionaries/create') }}"><i class="fa fa-btn fa-plus"></i>{{ _('辞書の新規作成') }}</a></li>
                                <li><a href="{{ url('/home/edit') }}"><i class="fa fa-btn fa-cog"></i>{{ _('ユーザー設定') }}</a></li>
                                <li><a href="{{ route('users.show', ['user' => Auth::user()->id]) }}"><i class="fa fa-btn fa-user"></i>{{ _('プロフィール') }}</a></li>
                                <li>{{ Form::open(['url' => '/logout']) }}
                                    <button><i class="fa fa-btn fa-sign-out"></i>{{ _('ログアウト') }}</button>
                                {{ Form::close() }}</li>
                            </ul>
                        </li>
                    @endif
                </ul>

                {{ Form::open([
                    'method' => 'GET',
                    'route' => 'dictionaries.index',
                    'class' => 'navbar-form navbar-right',
                    'role' => 'search',
                    'title' => _('辞書名、タグ、概要、および各お題の正しい表記の答え (textフィールド値) から検索します。'),
                ]) }}
                    <input type="search" name="search" class="form-control" placeholder="{{ _('辞書を検索') }}"
                        value="{{ Route::currentRouteName() === 'dictionaries.index' ? request('search') : '' }}">
                    <button class="btn btn-default fa fa-search"><span class="text-hide">検索</span></button>
                {{ Form::close() }}
            </div>
        </div>
    </nav>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ _('成功しました。') }}</div>
        @endif

        @if (count($errors) > 0)
        <ul class="list-group">
            @foreach ($errors->all() as $error)
                <li class="list-group-item list-group-item-danger"><span role="alert">{{ $error }}</span></li>
            @endforeach
        </ul>
        @endif
    </div>

    <main class="container @stack('main-classes')">
        @yield('content')
    </main>
    
    @yield('footer')

    <!-- JavaScripts -->
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    @if (config('app.debug'))
        <script src="{{ asset('js/debugbar-bugfix.es') }}"></script>
    @endif
    @stack('scripts')
</body>
</html>
