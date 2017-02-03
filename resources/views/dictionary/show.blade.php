<?php
use esperecyan\html_filter\Filter;
?>
@extends('dictionary.base')

@push('styles')
    <link href="{{ asset('css/dictionary.css') }}" rel="stylesheet" />
@endpush

@section('panel-body')
<div class="panel-body list-group">
    <section class="list-group-item including-labels">
        <h1 class="list-group-item-heading">{{ _('タグ') }}</h1>
        <ul>
            @foreach ($dictionary->tags as $tag)
            <li class="label label-default">{{ $tag->name }}</li>
            @endforeach
        </ul>
    </section>
    @if (isset($dictionary->summary))
    <section class="list-group-item">
        <h1 class="list-group-item-heading">{{ _('概要') }}</h1>
        <section>
            <h1></h1>
            {{ Html::convertField($dictionary->summary) }}
        </section>
    </section>
    @endif
    <section class="list-group-item">
        <h1 class="list-group-item-heading">{{ _('指定したゲーム用の辞書をダウンロード') }}</h1>
        <div class="btn-group">
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
        </div>
        @if (count($dictionary->files) > 0)
        <div class="btn-group">
            @foreach ([
                'csv' => sprintf(_('%s (CSVファイルのみ)'), _('汎用辞書')),
                'quiz' => sprintf(_('%s (テキストファイルのみ)'), _('Inteligenceω クイズ')),
            ] as $type => $name)
                <a href="{{ url()->current() }}?type={{ $type }}&scope=text" target="_blank" class="btn btn-default">
                    {{ $name }}
                </a>
            @endforeach
        </div>
        @endif
    </section>
    <section
        class="list-group-item row" id="copy-buttons" title="{{ _('JavaScriptを有効化する必要があります。') }}">
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
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/polyfills.es') }}"></script>
<script src="{{ asset('js/dictionary-clipboard.es') }}"></script>
@endpush
