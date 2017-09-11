<?php
use App\Dictionary;
?>
@extends('layouts.app')

@section('title', request()->filled('search') ? sprintf(e(_('「%s」の検索結果')), request('search')) : e(_('辞書一覧')))

@if (request()->filled('search'))
    @push('metas')
    <meta name="robots" content="noindex" />
    @endpush
@endif

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        {{ request()->filled('search') ? sprintf(e(_('辞書名・辞書の説明・各お題の解答に「%s」を含む辞書')), request('search')) : _('辞書一覧') }}
    </div>
    @component('dictionary.index-table', ['dictionaries' => $dictionaries])
        @if (request()->filled('search'))
            {{ _('見つかりませんでした。') }}
        @else
            {{ _('一つも投稿されていません。') }}
        @endif
    @endcomponent
</div>

{{ $dictionaries->links() }}
@endsection
