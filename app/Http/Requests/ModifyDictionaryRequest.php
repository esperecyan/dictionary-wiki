<?php

namespace App\Http\Requests;

use App\{Dictionary, Revision, File};
use App\Http\Controllers\DictionariesController;
use Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Validation\Validator;

/**
 * 辞書の作成・修正のリクエスト。
 */
class ModifyDictionaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /**
         * @var string[][]
         */
        $rules = [
            // 共通
            'tags' => ['string'],
            'summary' => ['string', 'max:' . Revision::MAX_SUMMARY_LENGTH],
            
            // 新規作成
            'category' => ['in:' . implode(',', Dictionary::CATEGORIES)],
            'locale' => [
                'string',
                'max:' . Dictionary::MAX_LOCALE_LENGTH,
                'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u',
            ],
            'uploading' => ['boolean'],
            
            // 辞書ファイルのアップロードによる新規作成。
            'dictionary' => ['file'],
            'type' => ['in:' . implode(',', array_keys(DictionariesController::TYPES)), 'nullable'],
            'name' => ['string', 'max:' . Dictionary::MAX_FIELD_LENGTH],
            
            // CSVの記述による新規作成時、および更新時の同梱ファイル追加・削除。
            'csv' => ['string'],
            'added-files' => ['array'],
            'added-files.*' => ['dictionary_file'],
            
            // 更新
            'deleted-file-names' => ['array'],
            'deleted-file-names.*' => ['string'],
        ];
        
        switch (Route::currentRouteName()) {
            case 'dictionaries.store':
                $requiredRuleKeys = ['category', 'locale'];
                $requiredRuleKeys[] = $this->input('uploading') === '1' ? 'dictionary' : 'csv';
                break;
            case 'dictionaries.update':
                $requiredRuleKeys = ['summary'];
                break;
        }
        
        return $this->requireRules($rules, $requiredRuleKeys);
    }
    
    /**
     * 指定したキーのバリデーションルールに、必須であること (required) を追加します。
     * 指定されなかったキーで文字列 (string) の場合、nullを許容すること (nullable) を追加します。
     *
     * @prama (string|string[])[] $rules
     * @param string[] $keys
     * @return string[][]
     */
    public function requireRules(array $rules, array $keys): array
    {
        foreach ($rules as $key => &$rule) {
            if (in_array($key, $keys)) {
                array_push($rule, 'required');
            } elseif (in_array('string', $rule)) {
                array_push($rule, 'nullable');
            }
        }
        return $rules;
    }
    
    /**
     * @inheritDoc
     */
    public function messages()
    {
        return [
            'dictionary_file' => _('「error」と「critical」を修正する必要があります。'),
        ] + parent::messages();
    }
    
    /**
     * 辞書のバリデートを行います。
     *
     * @param \App\Http\Controllers\DictionariesController $controller
     * @param \App\Dictionary|null $dictionary
     * @return void
     */
    public function validateDictionary(DictionariesController $controller, Dictionary $dictionary = null)
    {
        $filenames = array_map(function (UploadedFile $addedFile): string {
            return $addedFile->getClientOriginalName();
        }, $this->file('added-files', []));
            
        if ($dictionary) {
            $controller->validate(
                $this,
                ['deleted-file-names.*' => "exists:files,name,dictionary_id,$dictionary->id"]
            );
            
            $filenames = array_unique(array_merge($filenames, array_diff(
                $dictionary->files()->pluck('name')->toArray(),
                $this->input('deleted-file-names', [])
            )));
        }
        
        $controller->validate($this, ['csv' => [
            'required',
            'string',
            'dictionary' . ($filenames ? ':' . implode(',', $filenames) : ''),
        ]], [
            'dictionary' => _('「error」と「critical」を修正する必要があります。'),
        ]);
    }
}
