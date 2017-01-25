<?php
use App\Dictionary;
use bantu\IniGetWrapper\IniGetWrapper;
use ScriptFUSION\Byte\ByteFormatter;
?>
@extends('dictionary.modify')

@section('fieldsets')
    <fieldset class="panel panel-default" id="upload-fieldset">
        <legend class="panel-heading">
            <label>
                {{ Form::radio('uploading', '1', true) }}
                {{ _('辞書のアップロード') }}
            </label>
        </legend>
        <dl class="panel-body" id="upload-list">
            <div class="form-group{{ $errors->has('dictionary') ? ' has-error' : '' }}">
                <dt class="control-label col-md-3">{{ Form::label('dictionary', _('辞書ファイル')) }}</dt>
                <dd class="col-md-8">
                    {{ Form::file('dictionary') }}
                    <small class="help-block">
                        {{ sprintf(
                            _('アップロード可能なファイルサイズは最大 %s です。'),
                            (new ByteFormatter())->format((new IniGetWrapper())->getBytes('upload_max_filesize'))
                        ) }}
                    </small>
                    <small class="help-block">
                        {{ _('辞書ファイルとしてZIPファイルを選択した場合、「自動判定」では「汎用辞書」が選択されます。「Inteligenceω クイズ」の場合は明示的に指定してください。') }}
                    </small>
                </dd>
            </div>
            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                <dt class="control-label col-md-3">{{ Form::label('type', _('アップロードする辞書の種類')) }}</dt>
                <dd class="col-md-8">
                    {{ Form::select('type', [
                        '' => _('自動判定'),
                        'csv' => _('汎用辞書'),
                        'cfq' => _('キャッチフィーリング'),
                        'dat' => _('きゃっちま'),
                        'quiz' => _('Inteligenceω クイズ'),
                        'siri' => _('Inteligenceω しりとり'),
                        'pictsense' => _('ピクトセンス'),
                    ], '', ['class' => 'form-control']) }}
                    <small class="help-block">
                        {{ _('辞書ファイルとしてZIPファイルを選択した場合、「自動判定」では「汎用辞書」が選択されます。「Inteligenceω クイズ」の場合は明示的に指定してください。') }}
                    </small>
                </dd>
            </div>
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <dt class="control-label col-md-3">{{ Form::label('name', _('辞書名')) }}</dt>
                <dd class="col-md-8">
                    {{ Form::text(
                        'name',
                        null,
                        ['maxlength' => Dictionary::MAX_FIELD_LENGTH, 'class' => 'form-control']
                    ) }}
                    <small class="help-block">
                        {{ _('省略した場合、ファイル名を基に決定します。また、辞書名が設定された汎用辞書の場合、この指定は無視されます。') }}
                    </small>
                </dd>
            </div>
        </dl>
    </fieldset>
    <fieldset class="panel panel-default" id="input-fieldset">
        <legend class="panel-heading">
            <label>
                {{ Form::radio('uploading', '0') }}
                {{ _('フォーム上で作成') }}
            </label>
        </legend>
        <dl class="panel-body" id="input-list">
            @include('dictionary.csv')
        </dl>
    </fieldset>
@endsection

@section('submit-label', e(_('新規作成')))
