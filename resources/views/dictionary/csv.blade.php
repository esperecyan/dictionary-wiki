<div class="form-group{{ $errors->has('csv') ? ' has-error' : '' }}">
    <dt class="control-label col-md-12">{{ Form::label('csv', _('CSV')) }}</label></dt>
    <dd class="col-md-12">
        <ul role="tablist" class="nav nav-tabs">
            <li role="presentation" class="active">
                <a role="tab" aria-controls="source" aria-selected="true">
                    {{ _('ソースを編集') }}
                </a>
            </li>
            <li role="presentation" class="disabled">
                <a role="tab" aria-controls="table" aria-disabled="true">
                    {{ _('表形式で編集') }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" id="source" class="tab-pane active">
                <ul id="csv-errors"></ul>
                {{ Form::textarea(
                    'csv',
                    old('csv', isset($dictionary)
                        ? $dictionary->revision->data
                        : "text,answer,description,@title,@summary\n,,,[辞書名],《辞書の概要》"),
                    ['class' => 'form-control', 'required' => isset($dictonary) ?: null]
                ) }}
            </div>
            <div role="tabpanel" id="table" class="tab-pane">
                {{ Form::errors('csv') }}
            </div>
            <datalist id="table-context-menu">
                <option value="undo">{{ _('元に戻す') }}</option>
                <option value="redo">{{ _('やり直し') }}</option>
                <option value="separator">---------</option>
                <option value="row_above">{{ _('上に行を挿入') }}</option>
                <option value="row_below">{{ _('下に行を挿入') }}</option>
                <option value="col_left">{{ _('左に列を挿入') }}</option>
                <option value="col_right">{{ _('右に列を挿入') }}</option>
                <option value="remove_row">{{ _('行を削除') }}</option>
                <option value="remove_col">{{ _('列を削除') }}</option>
            </datalist>
        </div>
    </dd>
</div>
<div class="form-group{{ $errors->has('added-files[]') ? ' has-error' : '' }}">
    <dt class="control-label col-md-3">
        {{ Form::label('added-files[]', isset($dictionary) ? _('追加するファイル') : _('同梱するファイル')) }}
    </dt>
    <dd class="col-md-8">
        {{ Form::file('added-files[]', ['multiple']) }}
        <small class="help-block">
            {!! sprintf(
                e(_('アップロード可能なファイル形式・ファイル名については、%sをご覧ください')),
                '<a rel="external" href="' . e(_('https://github.com/esperecyan/dictionary/blob/master/dictionary.md#画像音声動画ファイルを含む場合のファイル形式')) . '" target="_blank">'
                    . e(_('画像音声動画ファイルを含む場合のファイル形式'))
                . '</a>'
            ) !!}
        </small>
        <small class="help-block">
            {{ sprintf(_('%d個まで選択可能です。'), ini_get('max_file_uploads')) }}
        </small>
        @if (isset($dictionary))
        <small class="help-block">
            {{ _('同名のファイルが既に含まれていれば、上書きされます。') }}
        </small>
        @endif
    </dd>
</div>
