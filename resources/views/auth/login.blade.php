@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {!! csrf_field() !!}
                        
                        <div class="form-group">
                            <div class="col-md-offset-1">
                                @foreach (config('auth.services') as $service)
                                    <button type="submit" name="provider" value="{{ $service }}" class="btn btn-primary">
                                        <i class="fa fa-btn fa-sign-in"></i>{{ $service }}でログイン
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="col-md-offset-1">
                                {!! sprintf(
                                    e(_('あなたが既にユーザー登録を済ませており、'
                                        . '登録済みの外部アカウントとは別の外部アカウントでログインしようとした場合、'
                                        . '別のユーザーとして登録されます。'
                                        . '既存のユーザーに新しい外部アカウントを追加するには、'
                                        . '登録済みの外部アカウントでログイン後、%sから追加してください。')),
                                    link_to('home/edit', 'ユーザー設定')
                                ) !!}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
