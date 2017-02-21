<?php
use Illuminate\Support\HtmlString;
?>
@extends('layouts.app')

@section('title', e($shownUser->name . ' | ' . _('ユーザー')))

@push('styles')
    <link href="{{ asset('css/nav-tabs.css') }}" rel="stylesheet" />
@endpush

@section('content')
<?php $postsCount = $shownUser->forumCategory->threads()->withCount('posts')->get()->sum('posts_count'); ?>
<nav class="tabs">
    <ul class="nav nav-tabs nav-tabs-justified">
        @foreach ([
            'show'               => _('詳細'),
            'dictionaries.index' => _('個人用辞書'),
            'threads.index'      => new HtmlString(
                e(_('コメント欄')) . ($postsCount > 0 ? " <span class=\"badge\">$postsCount</span>" : '')
            ),
        ] as $action => $text)
            <?php $route = "users.$action"; ?>
            <li @if ($route === Route::currentRouteName())class="active"@endif>{{ link_to_route(
                $route,
                $text,
                [($action === 'threads.index' ? 'model' : 'user') => $shownUser->id]
            ) }}</li>
        @endforeach
    </ul>
</nav>

<div class="panel panel-default">
    <h1 class="panel-heading panel-title">
        <img src="{{ $shownUser->avatar ?: asset('img/no-avatar.png') }}" alt="" />
        <bdi>{{ $shownUser->name }}</bdi>
    </h1>
    @yield('panel-body')
</div>
@endsection
