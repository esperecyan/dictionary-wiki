@extends('layouts.app')

@section('title', e(_('ユーザー一覧')))

@push('styles')
    <link href="{{ asset('css/external-accounts.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">{{ _('ユーザー一覧') }}</div>
    <table class="panel-body table">
        <thead>
            <tr>
                <th>@sortablelink('user-name', _('ユーザー名'))</th>
                <th>{{ _('外部アカウント') }}</th>
                <th class="text-right">@sortablelink('revision_count', _('編集数'))</th>
                <th>@sortablelink('revision_created_at', _('最終辞書更新日時'))</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $shownUser)
                <tr>
                    <th>{{ show_user($shownUser) }}</th>
                    <td>
                        @foreach($shownUser->links as $provider => $url)
                            <a href="{{ $url }}" rel="external" target="_blank" class="service">
                                @if ($provider === 'google')<span class="fa-stack">
                                    <i class="fa fa-square fa-stack-1x"></i>
                                    <i class="fa fa-google fa-inverse fa-stack-1x"></i>
                                </span>@else<i class="fa fa-{{ $provider }}-square">
                                </i>@endif<span class="text-hide">{{ $provider }}</span></a>
                        @endforeach
                    </td>
                    <td class="text-right">{{ $shownUser->revision_count }}</td>
                    <td>@if ($shownUser->revision_created_at)
                        {{ show_time($shownUser->revision_created_at) }}
                    @endif</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endsection
