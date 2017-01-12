<?php
use esperecyan\html_filter\Filter;
use Illuminate\Support\HtmlString;
use App\ExternalAccount;

$shownUser->load('externalAccounts');
$revisions = $shownUser->revisions()->with('dictionary')->orderBy('created_at', 'DESC')->getResults();
?>
@extends('layouts.app')

@section('title', e($shownUser->name . ' | ' . _('ユーザー')))

@section('styles')
    <link href="{{ asset('css/nav-tabs.css') }}" rel="stylesheet" />
@endsection

@section('content')
<nav class="tabs">
    <ul class="nav nav-tabs nav-tabs-justified">
        <li class="active"><a href="#">{{ _('詳細') }}</a></li>
        <?php $threadsCount = $shownUser->forumCategory()->withCount('threads')->first()->threads_count; ?>
        <li>{{ link_to_route(
            'users.threads.index',
            new HtmlString(e(_('コメント欄')) . ($threadsCount > 0 ? " <span class=\"badge\">$threadsCount</span>" : '')),
            ['model' => $shownUser->id],
            $threadsCount === 0 ? ['rel' => 'nofollow'] : []
        ) }}</li>
    </ul>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <img src="{{ $shownUser->avatar ?: asset('img/no-avatar.png') }}" alt="" />
                    <bdi>{{ $shownUser->name }}</bdi>
                </div>
                <dl class="panel-body">
                    <dt>外部アカウント</dt>
                        <dd class="service-buttons"> @foreach($shownUser->links as $provider => $url)
                            <a href="{{ $url }}" rel="external" target="_blank" class="btn btn-info">
                                <i class="fa fa-{{ $provider }}"></i>
                                {{ ExternalAccount::getServiceDisplayName($provider) }}
                            </a>
                        @endforeach </dd>
                    <dt>プロフィール</dt>
                        <dd> @if (!is_null($shownUser->profile))
                            {!! (new Filter())->filter(Markdown::convertToHtml($shownUser->profile)) !!}
                        @endif </dd>
                    <dt>編集数</dt>
                        <dd>{{ count($revisions) }}</dd>
                    <dt>編集一覧</dt>
                        <dd>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ _('更新日時') }}</th>
                                        <th>{{ _('更新した辞書') }}</th>
                                        <th>{{ _('更新内容の要約') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($revisions as $revision)
                                        @if ($revision->dictionary)
                                            <tr>
                                                <td>{{ link_to_route(
                                                    'dictionaries.revisions.show',
                                                    show_time($revision->created_at),
                                                    [
                                                        'dictionary' => $revision->dictionary->id,
                                                        'revision' => $revision->id,
                                                    ]
                                                ) }}</td>
                                                <th>{{ link_to_route(
                                                    'dictionaries.show',
                                                    $revision->dictionary->title,
                                                    ['dictionary' => $revision->dictionary->id]
                                                ) }}</th>
                                                <td>{{ $revision->summary }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
