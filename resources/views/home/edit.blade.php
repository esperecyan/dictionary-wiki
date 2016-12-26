@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ _('ユーザー設定') }}</div>
                <div class="panel-body">
                    {!! Form::model(Auth::user(), ['class' => 'form-horizontal']) !!}
                        
                        <div class="form-group">
                            <label class="col-md-4 control-label">ID</label>

                            <div class="col-md-6">
                                {{ Auth::user()->id }}
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-12">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{ _('状態') }}</th>
                                            <th>{{ _('名前') }}</th>
                                            <th>{{ _('アバター') }}</th>
                                            <th>{{ _('メール') }}</th>
                                            <th>{{ _('公開') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $externalAccounts = Auth::user()->externalAccounts;
                                        ?>
                                        @foreach (config('auth.services') as $service)
                                            <?php
                                            $externalAccount = null;
                                            foreach ($externalAccounts as $account) {
                                                if ($account->provider === $service) {
                                                    $externalAccount = $account;
                                                }
                                            }
                                            $available = $externalAccount && $externalAccount->available;
                                            ?>
                                            <tr>
                                                <td>
                                                    @if ($externalAccount && $externalAccount->link)
                                                        {!! link_to($externalAccount->link, $service); !!}
                                                    @else
                                                        {{ $service }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($externalAccount)
                                                        {{ $externalAccount->available ? _('有効') : _('無効')}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($externalAccount)
                                                        <label @if (!$available){!! ' title="' . e(_('この名前を公開するには、連携を許可する必要があります。')) .'"' !!} @endif>
                                                            {!! Form::radio(
                                                                'name-provider',
                                                                $service,
                                                                Auth::user()->nameProvider->provider === $service,
                                                                ['disabled' => $available ? null : '']
                                                            ); !!}
                                                            {{ $externalAccount->name }}
                                                        </label>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($externalAccount && $externalAccount->avatar)
                                                        <label @if (!$available) {!! ' title="' . e(_('このアバターを公開するには、連携を許可する必要があります。')) .'"' !!} @endif>
                                                            {!! Form::radio(
                                                                'avatar-provider',
                                                                $service,
                                                                Auth::user()->avatarProvider->provider === $service,
                                                                ['disabled' => $available ? null : '']
                                                            ) !!}
                                                            {!! Html::image(
                                                                $externalAccount->avatar,
                                                                sprintf(_('%sのアバター'), $service),
                                                                ['width' => '100']
                                                            ) !!}
                                                        </label>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($externalAccount && $externalAccount->email)
                                                        <label @if (!$available) {!! ' title="' . e(_('このメールアドレスに通知を送るには、連携を許可する必要があります。')) .'"' !!} @endif>
                                                            {!! Form::radio(
                                                                'email-provider',
                                                                $service,
                                                                Auth::user()->emailProvider->provider === $service,
                                                                ['disabled' => $available ? null : '']
                                                            ) !!}
                                                            {{ $externalAccount->email }}
                                                        </label>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($externalAccount && $externalAccount->link)
                                                        {!! Form::checkbox(
                                                            'public[]',
                                                            $service,
                                                            $externalAccount->public,
                                                            [
                                                                'disabled' => $available ? null : '',
                                                                'title' => $available ? null : _('アカウントへのリンクを公開するには、連携を許可する必要があります。'),
                                                            ]
                                                        ) !!}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($externalAccount)
                                                        <button formaction="{{ route('users.external-accounts.update') }}" name="provider" value="{{ $service }}">
                                                            {{ _('更新') }}
                                                        </button>
                                                        <button name="disconnect" value="{{ $service }}"{!! $userCanDisconnect ? '' : ' disabled="" title="' . e(_('連携を解除するには、別の外部アカウントを登録する必要があります。')) . '"' !!}>
                                                            {{ _('連携を解除') }}
                                                        </button>
                                                    @else
                                                        <button formaction="{{ route('users.external-accounts.store') }}" name="provider" value="{{ $service }}">
                                                            {{ _('連携') }}
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                ※ここで選択するメールアドレスは通知用のものであり、他のユーザーには公開されません。

                                @if ($errors->has('profile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('profile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                            {!! Form::label(_('自己紹介'), null, ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('profile', null, array_merge(['class' => 'form-control'])) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                {!! Form::button('<i class="fa fa-btn fa-edit"></i>' . _('更新'), ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
