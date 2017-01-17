<?php
use App\Dictionary;
?>
@extends('layouts.app')

@section('title', e(_('辞書一覧')))

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">{{ _('辞書一覧') }}</div>
    <table class="panel-body table">
        <thead>
            <tr>
                <th>{{ _('カテゴリ') }}</th>
                <th>{{ _('辞書名') }}</th>
                <th class="text-right">{{ _('語数') }}</th>
                <th>{{ _('更新日時') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($dictionaries->sortByDesc(function (Dictionary $dictionary) {
                return $dictionary->revision->created_at;
            }) as $dictionary)
                <tr>
                    <td>{{ $dictionary->categoryName }}</td>
                    <th><bdi>{{
                        link_to_route('dictionaries.show', $dictionary->title, ['dictionary' => $dictionary->id])
                    }}</bdi></th>
                    <td class="text-right">{{ $dictionary->words }}</td>
                    <td>{!! show_time($dictionary->revision->created_at) !!}</td>
                    <td>{{ Html::showDictionaryWarnings($dictionary) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
