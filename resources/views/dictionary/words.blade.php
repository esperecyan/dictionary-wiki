@extends('dictionary.base')

@section('title')@@parent — {{ _('お題一覧') }}@endsection

@push('styles')
    <link href="{{ asset('css/dictionary-words.css') }}" rel="stylesheet" />
@endpush

@section('panel-body')
<table class="panel-body table">
    <thead>
        <tr>
        @foreach (($fieldNames = array_shift($records)) as $fieldName)
            <th>{{ $fieldName }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach ($records as $fields)
        <tr>
        @foreach ($fields as $i => $field)
            <td>{{ $field }}</td>
        @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
