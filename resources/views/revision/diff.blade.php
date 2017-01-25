<?php
use App\Revision;
use Illuminate\Support\HtmlString;

$diffData = Revision::diffTables($revisions[0]->csv, $revisions[1]->csv);
if (isset($diffData[1])) {
    $diffRender = new coopy_DiffRender();
    $diffRender->usePrettyArrows(false);
    $diffRender->render(new coopy_PhpTableView($diffData));
}
?>
@extends('layouts.app')

@section('title', e(sprintf(_('「%s」の差分'), $dictionary->title)))

@push('styles')
    <link href="{{ asset('css/diffrender-sample.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-default">
    <h1 class="panel-heading panel-title">
        {!! sprintf(e(_('%sの差分')), link_to_route(
            'dictionaries.show',
            new HtmlString('<bdi>' . e($dictionary->title) . '</bdi>'),
            [$dictionary],
            ['class' => 'text-primary']
        )) !!}
    </h1>
    <div class="panel-body list-group">
        <table class="table">
            <thead>
                <tr>
                    <td></td>
                    <th>{{ _('更新日時') }}</th>
                    <th>{{ _('更新内容の要約') }}</th>
                    <th>{{ _('ユーザー') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($revisions as $i => $revision)
                    <tr>
                        <th>{{ $i === 0 ? _('旧') : _('新') }}</th>
                        <td>{{ link_to_route(
                            'dictionaries.revisions.show',
                            show_time($revision->created_at),
                            ['dictionary' => $dictionary->id, 'revision' => $revision->id]
                        ) }}</td>
                        <td>{{ $revision->summary }}</td>
                        <td>{{ show_user($revision->user) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($tags)
        <section class="list-group-item">
            <h1 class="list-group-item-heading">{{ _('タグの変更') }}</h1>
            <dl class="including-multiple-values">
                @if ($tags['added'])
                <div>
                    <dt>{{ _('追加されたタグ') }}</dt>
                    @foreach ($tags['added'] as $tag)
                    <dd>{{ $tag }}</dd>
                    @endforeach
                </div>
                @endif
                @if ($tags['deleted'])
                <div>
                    <dt>{{ _('削除されたタグ') }}</dt>
                    @foreach ($tags['deleted'] as $tag)
                    <dd>{{ $tag }}</dd>
                    @endforeach
                </div>
                @endif
            </dl>
        </section>
        @endif
        
        @if($files)
        <section class="list-group-item">
            <h1 class="list-group-item-heading">{{ _('ファイルの変更') }}</h1>
            <dl class="including-multiple-values">
                @if ($files['added'])
                <div>
                    <dt>{{ _('追加されたファイル') }}</dt>
                    @foreach ($files['added'] as $filename)
                    <dd>{{ $filename }}</dd>
                    @endforeach
                </div>
                @endif
                @if ($files['deleted'])
                <div>
                    <dt>{{ _('削除されたファイル') }}</dt>
                    @foreach ($files['deleted'] as $filename)
                    <dd>{{ $filename }}</dd>
                    @endforeach
                </div>
                @endif
                @if ($files['modified'])
                <div>
                    <dt>{{ _('変更されたファイル') }}</dt>
                    @foreach ($files['modified'] as $filename)
                    <dd>{{ $filename }}</dd>
                    @endforeach
                </div>
                @endif
            </dl>
        </section>
        @endif
        
        @if (isset($diffRender))
        <section class="list-group-item highlighter">
            {!! $diffRender->html() !!}
        </section>
        @endif
    </div>
</div>
@endsection
