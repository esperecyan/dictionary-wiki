@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/sticky-footer-navbar.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body">
                    Your Application's Landing Page.
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer navbar navbar-default">
    <nav>
        <div class="container">
            <div class="collapse navbar-collapse" id="site-feedback">
                <ul class="nav navbar-nav navbar-right">
                    <li>{{ link_to_route('site.threads.index', _('サイトのフィードバック')) }}</li>
                </ul>
            </div>
        </div>
    </nav>
</footer>
@endsection
