@extends('dictionary.modify')

@section('title')@parent — {{ _('編集') }}@endsection

@section('commons')
    @parent
    @include('dictionary.csv')
    @if (count($dictionary->files) > 0)
    <div class="form-group{{ $errors->has('deleted-file-names[]') ? ' has-error' : '' }}">
        <dt class="control-label col-md-3">
            {{ Form::label('deleted-file-names[]', _('削除するファイル')) }}
        </dt>
        <dd class="col-md-8">
            <ul class="list-group">
            @foreach ($dictionary->files as $file)
                <li class="list-group-item">
                    <label>{{ Form::checkbox('deleted-file-names[]', $file->name) }} {{$file->name}}</label>
                </li>
            @endforeach
            </ul>
            {{ Form::errors('deleted-file-names[]') }}
        </dd>
    </div>
    @endif
@endsection

@section('submit-label', e(_('編集を反映')))
