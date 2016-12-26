@extends('auth.auth')

@section('title', e(_('ログイン')))

@section('action', e(route('users.login')))

@section('form-before-end')
    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div>
        </div>
    </div>
@endsection
