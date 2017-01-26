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
