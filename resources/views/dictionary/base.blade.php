<?php
use Illuminate\Support\HtmlString;
?>
@extends('layouts.app')

@section('title', e($dictionary->title ?? _('辞書の新規作成')))

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
                <?php $route = "dictionaries.$action"; ?>
                <li @if ($route === Route::currentRouteName())class="active"@endif>{{ link_to_route(
                    $route,
                    $text,
                    [($action === 'threads.index' ? 'model' : 'dictionary') => $dictionary->id]
                ) }}</li>
            @endforeach
        </ul>
    </nav>
@endif
<div class="panel panel-default">
    <h1 class="panel-heading panel-title">
        @if (isset($dictionary))
            <span class="label label-primary">{{ $dictionary->categoryName }}</span>
            <bdi>{{ $dictionary->title }}</bdi>
            {{ Html::showDictionaryWarnings($dictionary) }}
        @else
            {{ _('辞書の新規作成') }}
        @endif
    </h1>
    @yield('panel-body')
</div>
@endsection
