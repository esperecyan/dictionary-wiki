<?php
use esperecyan\html_filter\Filter;
use Illuminate\Support\HtmlString;
?>
@extends('layouts.app')

@section('title', e($dictionary->title))

@push('styles')
    <link href="{{ asset('css/dictionary.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/nav-tabs.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="tabs">
    <ul class="nav nav-tabs nav-tabs-justified">
        <li class="active"><a href="#">{{ _('詳細') }}</a></li>
        <?php $postsCount = $dictionary->forumCategory->threads()->withCount('posts')->get()->sum('posts_count'); ?>
        <li>{{ link_to_route(
            'dictionaries.threads.index',
            new HtmlString(e(_('コメント欄')) . ($postsCount > 0 ? " <span class=\"badge\">$postsCount</span>" : '')),
            ['model' => $dictionary->id],
            $postsCount === 0 ? ['rel' => 'nofollow'] : []
        ) }}</li>
    </ul>
</nav>
<div class="panel panel-default">
    <h1 class="panel-heading panel-title">
        <span class="label label-primary">{{ $dictionary->categoryName }}</span>
        <bdi>{{ $dictionary->title }}</bdi>
        {{ Html::showDictionaryWarnings($dictionary) }}
        <dl class="badge"><dt>{{ _('語数') }}</dt><dd>{{ $dictionary->words }}</dd></dl>
    </h1>
    <dl class="panel-body list-group">
        <div class="list-group-item including-multiple-values including-labels">
            <dt class="list-group-item-heading">{{ _('タグ') }}</dt>
            @foreach ($dictionary->tags as $tag)
                <dd class="label label-default">{{ $tag->name }}</dd>
            @endforeach
        </div>
        @if (isset($dictionary->summary))
        <div class="list-group-item">
            <dt class="list-group-item-heading">{{ _('概要') }}</dt>
            <dd>
                {!! (new Filter())->filter(Markdown::convertToHtml($dictionary->summary)) !!}
            </dd>
        </div>
        @endif
        <div class="list-group-item">
            <dt class="list-group-item-heading">{{ _('指定したゲーム用の辞書をダウンロード') }}</dt>
            <dd class="btn-group">
                @foreach ([
                    'csv' => _('汎用辞書'),
                    'cfq' => _('キャッチフィーリング'),
                    'dat' => _('きゃっちま'),
                    'quiz' => _('Inteligenceω クイズ'),
                    'siri' => _('Inteligenceω しりとり'),
                    'pictsense' => _('ピクトセンス'),
                ] as $type => $name)
                    <a href="{{ url()->current() }}?type={{ $type }}" target="_blank" class="btn btn-default">
                        {{ $name }}
                    </a>
                @endforeach
            </dd>
            @if (count($dictionary->files) > 0)
            <dd class="btn-group">
                @foreach ([
                    'csv' => sprintf(_('%s (CSVファイルのみ)'), _('汎用辞書')),
                    'quiz' => sprintf(_('%s (テキストファイルのみ)'), _('Inteligenceω クイズ')),
                ] as $type => $name)
                    <a href="{{ url()->current() }}?type={{ $type }}&scope=text" target="_blank" class="btn btn-default">
                        {{ $name }}
                    </a>
                @endforeach
            </dd>
            @endif
        </div>
        <div
            class="list-group-item" id="copy-buttons" title="{{ _('JavaScriptを有効化する必要があります。') }}">
            <dt></dt>
            <dd class="row">
                <ul class="col-md-8">
                    <li>
                        <button type="button" name="copy" value="quiz" disabled="" class="btn btn-default"
                            data-file="{{ _('画像・音声ファイルを含むInteligenceω クイズ辞書は、ダウンロードする必要があります。') }}">
                            <i class="fa fa-clipboard"></i>
                            {{ _('Inteligenceω クイズの辞書のURLをクリップボードにコピー') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" name="copy" value="siri" disabled="" class="btn btn-default">
                            <i class="fa fa-clipboard"></i>
                            {{ _('Inteligenceω しりとりの辞書のURLをクリップボードにコピー') }}
                        </button>
                    </li>
                    <li>
                        <button type="button" name="copy" value="pictsense" disabled="" class="btn btn-default">
                            <i class="fa fa-clipboard"></i>
                            {{ _('ピクトセンスの辞書の内容をクリップボードにコピー') }}
                        </button>
                    </li>
                </ul>
                <div class="col-md-4">
                    <div
                        role="alert"
                        data-success="{{ _('コピーしました。') }}"
                        data-failure="{{ _('クリップボードに書き込めませんでした。') }}">
                    </div>
                </div>
            </dd>
        </div>
        <div class="list-group-item{{ count($records[0]) > 6 ? ' many-columns' : '' }}" id="words">
            <dt class="list-group-item-heading">お題一覧</dt>
            <table class="table">
                <thead>
                    <tr>
                    @foreach (($fieldNames = array_shift($records)) as $fieldName)
                        <th>{{ $fieldName }}</th>
                    @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach ($records as $fields)
                    <tr>
                    @foreach ($fields as $i => $field)
                        <td>{{ $field }}</td>
                    @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="list-group-item">
            <dt class="list-group-item-heading">更新履歴</dt>
            <dd>
                {{ Form::open(['method' => 'GET', 'route' => ['dictionaries.revisions.diff', $dictionary]]) }}
                    @if (count($revisions) > 1)
                    {{ Form::submit(_('選択したバージョン同士を比較'), ['class' => 'btn btn-default']) }}
                    @endif
                    <table class="table">
                        <thead>
                            <tr>
                                <td></td>
                                <th>{{ _('更新日時') }}</th>
                                <th>{{ _('更新内容の要約') }}</th>
                                <th>{{ _('ユーザー') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($revisions as $i => $revision)
                                <tr>
                                    <td>@if (count($revisions) > 1)
                                        {{ Form::checkbox('revisions[]', $revision->id, $i < 2) }}
                                    @endif</td>
                                    <th>{{ link_to_route(
                                        'dictionaries.revisions.show',
                                        show_time($revision->created_at),
                                        ['dictionary' => $dictionary->id, 'revision' => $revision->id]
                                    ) }}</th>
                                    <td>{{ $revision->summary }}</td>
                                    <td>{{ show_user($revision->user) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (count($revisions) > 1)
                    {{ Form::submit(_('選択したバージョン同士を比較'), ['class' => 'btn btn-default']) }}
                    @endif
                {{ Form::close() }}
            </dd>
        </div>
        <div class="list-group-item">
            <dt></dt>
            <dd class="text-center">
                <a
                    href="{{ route('dictionaries.edit', ['dictionary' => $dictionary->id]) }}"
                    class="btn btn-default btn-lg">
                    {{ _('この辞書を編集する') }}
                </a>
            </dd>
        </div>
    </dl>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/polyfills.es') }}"></script>
<script src="{{ asset('js/form-dictionary-show.es') }}"></script>
<script src="{{ asset('js/dictionary-clipboard.es') }}"></script>
@endpush
