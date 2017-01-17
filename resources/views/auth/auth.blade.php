<?php
use App\ExternalAccount;
?>

@extends('layouts.app')

@section('metas')
    <meta name="robots" content="noindex" />
@endsection

@push('styles')
    <link href="{{ asset('css/external-accounts.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('title')</div>
    <div class="panel-body">
        <form class="form-horizontal" method="POST" action="@yield('action')">
            {{ csrf_field() }}

            <div class="social-login-buttons">
                @foreach (config('auth.services') as $service)
                    <button type="submit" name="provider" value="{{ $service }}" class="btn btn-primary">
                        <i class="fa fa-btn fa-{{ $service }}"></i>
                        <b>{{ ExternalAccount::getServiceDisplayName($service) }}</b>
                        <span>{{ _('のアカウントでログイン') }}</span>
                    </button>
                @endforeach
            </div>

            @yield('form-before-end')
        </form>
    </div>
</div>
@endsection
