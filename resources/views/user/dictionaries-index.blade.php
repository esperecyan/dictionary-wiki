@extends('user.base')

@section('title', e($shownUser->name . ' — ' . _('個人用辞書') . ' | ' . _('ユーザー')))

@section('panel-body')
    @component('dictionary.index-table', ['dictionaries' => $dictionaries])
        {{ _('一つも投稿されていません。') }}
    @endcomponent
@endsection

@section('content')
    @parent
    {{ $dictionaries->links() }}
@endsection
