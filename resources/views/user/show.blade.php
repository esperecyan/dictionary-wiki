<?php
use esperecyan\html_filter\Filter;
use App\ExternalAccount;

$shownUser->load('externalAccounts');
$revisions = $shownUser->revisions()->with('dictionary')->orderBy('created_at', 'DESC')->get();
?>
@extends('user.base')

@section('panel-body')
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
        <dd>{{ $shownUser->revision_count }}</dd>
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
                                    ['dictionary' => $revision->dictionary->id, 'revision' => $revision->id]
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
@endsection
