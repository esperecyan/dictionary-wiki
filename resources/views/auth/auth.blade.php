@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">@yield('title')</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="@yield('action')">
                        {{ csrf_field() }}
                        
                        <div class="form-group">
                            <div class="col-md-offset-1">
                                @foreach (config('auth.services') as $service)
                                    <button type="submit" name="provider" value="{{ $service }}" class="btn btn-primary">
                                        <i class="fa fa-btn fa-sign-in"></i>{{ $service }}でログイン
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        @yield('form-before-end')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
