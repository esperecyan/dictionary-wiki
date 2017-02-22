<?php
use Illuminate\Support\HtmlString;
?>
@extends('layouts.app')

@section('title', e($dictionary->title ?? _('辞書の新規作成')))

@if (isset($dictionary))
@push('metas')
    <link href="{{ route('dictionaries.revisions.index', ['dictionary' => $dictionary->id, 'type' => 'atom']) }}"
        rel="alternate" type="application/atom+xml" />
@endpush
@endif

@push('styles')
    <link href="{{ asset('css/dictionary.css') }}" rel="stylesheet" />
@endpush

@section('content')
@if (isset($dictionary))
    <?php $postsCount = $dictionary->forumCategory->threads()->withCount('posts')->get()->sum('posts_count'); ?>
    <nav class="tabs">
        <ul class="nav nav-tabs nav-tabs-justified">
            @foreach ([
                'show'            => _('ダウンロード'),
                'words'           => new HtmlString(e(_('お題一覧')) . " <span class=\"badge\">$dictionary->words</span>"),
                'revisions.index' => _('更新履歴'),
                'edit'            => _('編集'),
                'threads.index'   => new HtmlString(
                    e(_('コメント欄')) . ($postsCount > 0 ? " <span class=\"badge\">$postsCount</span>" : '')
                ),
            ] as $action => $text)
                <?php
                $route = "dictionaries.$action";
                $disabled
                    = $action === 'edit' && $dictionary->category === 'private' && !Gate::allows('update', $dictionary);
                ?>
                <li class="@if ($route === Route::currentRouteName())active @elseif ($disabled)disabled @endif"
                    @if ($disabled)aria-disabled="true" title="{{ _('個人用辞書は、辞書を作成したユーザーのみが編集できます。') }}"@endif>
                    {{ link_to_route(
                        $route,
                        $text,
                        [($action === 'threads.index' ? 'model' : 'dictionary') => $dictionary->id]
                    ) }}
                </li>
            @endforeach
        </ul>
    </nav>
@endif
<div class="panel panel-default">
    <h1 class="panel-heading panel-title">
        @if (isset($dictionary))
            <span class="label label-primary">
                <?php
                switch ($dictionary->category) {
                    case 'generic':
                        $categoryIcon = 'fa-globe';
                        break;
                    case 'specific':
                        $categoryIcon = 'fa-gamepad';
                        break;
                    case 'private':
                        $categoryIcon = 'fa-flask';
                        break;
                }
                ?>
                <i class="fa {{ $categoryIcon }}"></i>
                {{ $dictionary->categoryName }}
            </span>
            <bdi>{{ $dictionary->title }}</bdi>
            {{ Html::showDictionaryWarnings($dictionary) }}
            <a href="{{ route('dictionaries.revisions.index', ['dictionary' => $dictionary->id, 'type' => 'atom']) }}"
                target="_blank" class="btn btn-default fa fa-feed">
                <span class="text-hide">フィード</span>
            </a>
        @else
            {{ _('辞書の新規作成') }}
        @endif
    </h1>
    @yield('panel-body')
</div>
@endsection
