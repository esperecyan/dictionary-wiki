<?php
use App\Dictionary;
use Illuminate\Support\HtmlString;
?>

@extends('dictionary.modify')

@section('title', e(sprintf(_('「%s」の編集'), $dictionary->title)))
@section('title-with-link', sprintf(e(_('%sの編集')), link_to_route(
    'dictionaries.show',
    new HtmlString('<bdi>' . e($dictionary->title) . '</bdi>'),
    [$dictionary],
    ['class' => 'text-primary']
)))

@section('commons')
    @@parent
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
