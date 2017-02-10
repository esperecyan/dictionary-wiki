<?php
use App\{Dictionary, Tag, Revision};
?>
@extends('dictionary.base')

@push('styles')
    <link href="{{ asset('css/handsontable.full.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/handsontable.bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/form-dictionary.css') }}" rel="stylesheet" />
@endpush

@section('panel-body')
{{ Form::model($dictionary ?? new Dictionary(), [
    'route' => isset($dictionary) ? ['dictionaries.update', $dictionary->id] : 'dictionaries.store',
    'method' => isset($dictionary) ? 'PATCH' : 'POST',
    'files' => true,
    'name' => 'dictionary',
    'class' => 'panel-body form-horizontal',
]) }}
    <dl>
        @section('commons')
        <div class="form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
            <dt class="col-md-3 control-label">{{ Form::label('summary', _('更新内容')) }}</dt>
            <dd class="col-md-8">
                {{ Form::text('summary', null, [
                    'maxlength' => Revision::MAX_SUMMARY_LENGTH,
                    'class' => 'form-control',
                    'required' => isset($dictionary) ? '' : null,
                ]) }}
                <small class="help-block">
                    {{ _('更新履歴に表示される文です。') }}
                </small>
                @if (empty($dictionary))
                    <small class="help-block">
                        {{ _('辞書の新規作成時は省略することができます。') }}
                    </small>
                @endif
            </dd>
        </div>
        <div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
            <dt class="col-md-3 control-label">{{ Form::label('tags', _('タグ')) }}</dt>
            <dd class="col-md-8">
                {{ Form::textarea('tags', null, ['class' => 'form-control']) }}
                <small class="help-block">
                    {{ sprintf(_('改行区切りで最大%s個設定できます。'), Tag::MAX_TAGS) }}
                </small>
            </dd>
        </div>
        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
            <dt class="col-md-3 control-label">{{ Form::label('category', _('カテゴリ')) }}</dt>
            <dd class="col-md-8">
                {{ Form::select(
                    'category',
                    array_combine(Dictionary::CATEGORIES, array_map(function (string $category): string {
                        $dictionary = new Dictionary();
                        $dictionary->category = $category;
                        return $dictionary->categoryName;
                    }, Dictionary::CATEGORIES)),
                    null,
                    [
                        'required',
                        'placeholder' => _('選択してください'),
                        'class' => 'form-control',
                        'disabled' => isset($dictionary) ? '' : null,
                    ]
                ) }}
                @if (empty($dictionary))
                    <small class="help-block">
                        <strong class="text-danger">{{ _('後から変更することはできません。') }}</strong>
                    </small>
                @endif
            </dd>
        </div>
        <div class="form-group{{ $errors->has('locale') ? ' has-error' : '' }}">
            <dt class="col-md-3 control-label">{{ Form::label('locale', _('ロケール')) }}</dt>
            <dd class="col-md-8">
                {{ Form::text('locale', null, [
                    'required',
                    'pattern' => '[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*',
                    'title' => _('言語タグで指定してください。'),
                    'maxlength' => Dictionary::MAX_LOCALE_LENGTH,
                    'class' => 'form-control',
                    'id' => 'locale',
                    'disabled' => isset($dictionary) ? '' : null,
                ]) }}
                @if (empty($dictionary))
                    <small class="help-block">
                        {{ _('個々のお題の言語ではなく、どの言語向けの辞書かを指定します。 (たとえば、descriptionフィールドの言語)') }}
                    </small>
                @endif
            </dd>
        </div>
        @show
    </dl>
    @yield('fieldsets')
    <button
        data-default-message="@yield('submit-label')"
        data-progress-message="{{ _('処理中') }}"
        class="btn btn-default btn-lg center-block">
        @yield('submit-label')
    </button>
{{ Form::close() }}
@endsection

@push('scripts')
<script src="{{ asset('js/handsontable.full.js') }}"></script>
<script src="{{ asset('js/papaparse.js') }}"></script>
<script src="{{ asset('js/validator.js') }}"></script>
<script src="{{ asset('js/form-dictionary.es') }}"></script>
@endpush
