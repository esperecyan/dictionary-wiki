@extends('dictionary.base')

@section('title')@parent — {{ _('更新履歴') }}@endsection

@section('panel-body')
{{ Form::open(['method' => 'GET', 'route' => ['dictionaries.revisions.diff', $dictionary], 'class' => 'panel-body']) }}
    @if (count($revisions) > 1)
    {{ Form::submit(_('選択したバージョン同士を比較'), ['class' => 'btn btn-default']) }}
    @endif
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
                    <td>@if (count($revisions) > 1)
                        {{ Form::checkbox('revisions[]', $revision->id, $i < 2) }}
                    @endif</td>
                    <th>{{ link_to_route(
                        'dictionaries.revisions.show',
                        show_time($revision->created_at),
                        ['dictionary' => $dictionary->id, 'revision' => $revision->id]
                    ) }}</th>
                    <td>{{ $revision->summary }}</td>
                    <td>{{ show_user($revision->user) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if (count($revisions) > 1)
    {{ Form::submit(_('選択したバージョン同士を比較'), ['class' => 'btn btn-default']) }}
    @endif
{{ Form::close() }}
@endsection

@section('content')
    @parent
    {{ $revisions->links() }}
@endsection

@push('scripts')
<script src="{{ asset('js/polyfills.es') }}"></script>
<script src="{{ asset('js/dictionary-revisions.es') }}"></script>
@endpush
