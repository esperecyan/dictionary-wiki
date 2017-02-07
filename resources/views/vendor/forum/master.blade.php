@extends('layouts.app')

@section('title')
    @if (isset($thread))
        {{ $thread->title }} -
    @endif
    @if (isset($category))
        {{ $category->title }} -
    @endif
    {{ trans('forum::general.home_title') }}
@endsection

<?php
$parameters = ['type' => 'atom'];
if ($dictionary = request()->route('dictionary')) {
    $parameters['dictionary'] = $dictionary->id;
} elseif ($user = request()->route('user')) {
    $parameters['user'] = $user->id;
}

?>
@push('metas')
    <link href="{{ route(explode('.', request()->route()->getName())[0] . '.threads.index', $parameters) }}"
        rel="alternate" type="application/atom+xml" />
@endpush

@push('styles')
    <link href="{{ asset('css/forum-master.css') }}" rel="stylesheet" />
@endpush

@section('content')
    @include ('forum::partials.breadcrumbs')
    @include ('forum::partials.alerts')
@endsection

@push('scripts')
    <script src="{{ asset('js/forum-master.js') }}"></script>
@endpush
