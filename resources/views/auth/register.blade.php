@extends('auth.auth')

@section('title', e(_('ユーザー登録')))

@section('action', e(route('users.store')))

@section('form-before-end')
    <div>
        <div class="col-md-offset-1">
            {!! sprintf(
                e(_('あなたが既にユーザー登録を済ませており、'
                    . '登録済みの外部アカウントとは別の外部アカウントでログインしようとした場合、'
                    . '別のユーザーとして登録されます。'
                    . '既存のユーザーに新しい外部アカウントを追加するには、'
                    . '登録済みの外部アカウントで%1$s後、%2$sから追加してください。')),
                link_to_route('login', _('ログイン')),
                link_to('home/edit', _('ユーザー設定'))
            ) !!}
        </div>
    </div>
@endsection
