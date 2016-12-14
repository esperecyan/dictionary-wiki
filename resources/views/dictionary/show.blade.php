<?php
use esperecyan\html_filter\Filter;
?>
@extends('layouts.app')

@section('title', e($dictionary->title))

@section('styles')
    <link href="{{ asset('css/dictionary.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container"><div class="row"><div class="col-md-8 col-md-offset-2"><div class="panel panel-default">
    <h1 class="panel-heading panel-title">
        <span class="label label-primary">{{ $dictionary->categoryName }}</span>
        <bdi>{{ $dictionary->title }}</bdi>
        @if ($dictionary->regard)
            <span title="{{ _('ひらがな (カタカナ) 以外が答えに含まれるお題があります') }}">
                {{ _('ひらがな (カタカナ) 以外が答えに含まれるお題があります') }}
            </span>
        @endif
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
</div></div></div></div>
@endsection

@section('scripts')
<script src="{{ asset('js/form-dictionary-show.es') }}"></script>
@endsection
