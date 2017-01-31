<?php
use App\Dictionary;
?>
@extends('layouts.app')

@section('title', request()->has('search') ? sprintf(e(_('「%s」の検索結果')), request('search')) : e(_('辞書一覧')))

@if (request()->has('search'))
    @push('metas')
    <meta name="robots" content="noindex" />
    @endpush
@endif

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        {{ request()->has('search') ? sprintf(e(_('辞書名・辞書の説明・各お題の解答に「%s」を含む辞書')), request('search')) : _('辞書一覧') }}
    </div>
    @if (request()->has('search') && count($dictionaries) === 0)
        <div class="panel-body">
            {{ _('見つかりませんでした。') }}
        </div>
    @else
        <table class="panel-body table">
            <thead>
                <tr>
                    <th>{{ _('カテゴリ') }}</th>
                    <th>@sortablelink('title', _('辞書名'))</th>
                    <th class="text-right">@sortablelink('words', _('語数'))</th>
                    <th>@sortablelink('updated_at', _('更新日時'))</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictionaries as $dictionary)
                    <tr>
                        <td>{{ $dictionary->categoryName }}</td>
                        <th><bdi>{{
                            link_to_route('dictionaries.show', $dictionary->title, ['dictionary' => $dictionary->id])
                        }}</bdi></th>
                        <td class="text-right">{{ $dictionary->words }}</td>
                        <td>{!! show_time($dictionary->updated_at) !!}</td>
                        <td>{{ Html::showDictionaryWarnings($dictionary) }}</td>
                    </tr>
                @endforeach
            </tbody>
            @if (request()->has('search'))
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">
                        powered by
                        <a href="https://www.algolia.com/" target="_blank" rel="external noopener noreferrer">
                            <img src="{{ asset('img/Algolia_logo_bg-white.svg') }}" alt="Algolia" height="17" />
                        </a>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    @endif
</div>

{{ $dictionaries->links() }}
@endsection
