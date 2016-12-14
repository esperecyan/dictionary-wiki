@extends('layouts.app')

@section('title', e(_('ユーザー一覧')))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ _('ユーザー一覧') }}</div>
                <table class="panel-body table">
                    <thead>
                        <tr>
                            <th>{{ _('ユーザー名') }}</th>
                            <th>{{ _('外部アカウント') }}</th>
                            <th class="text-right">{{ _('編集数') }}</th>
                            <th>{{ _('最終辞書更新日時') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users->sortByDesc(function ($user) {
                            return count($user->revisions);
                        }) as $shownUser)
                            <tr>
                                <th>{{ show_user($shownUser) }}</th>
                                <td>
                                    @foreach($shownUser->links as $provider => $url)
                                        {!! link_to($url, $provider) !!}
                                    @endforeach
                                </td>
                                <td class="text-right">{{ count($shownUser->revisions) }}</td>
                                <td>@if ($shownUser->revisions->first())
                                    {!! show_time($shownUser->revisions->first()->created_at) !!}
                                @endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
