<?php
use App\Dictionary;
?>
@extends('layouts.app')

@section('title', e(_('辞書一覧')))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
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
                                <th><bdi>{{ link_to_route(
                                    'dictionaries.show',
                                    $dictionary->title,
                                    ['dictionary' => $dictionary->id]
                                ) }}</bdi></th>
                                <td class="text-right">{{ $dictionary->words }}</td>
                                <td>{!! show_time($dictionary->revision->created_at) !!}</td>
                                <td>
                                    @if ($dictionary->regard)
                                        <span title="{{ _('ひらがな (カタカナ) 以外が答えに含まれるお題があります') }}">
                                            {{ _('ひらがな (カタカナ) 以外が答えに含まれるお題があります') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
