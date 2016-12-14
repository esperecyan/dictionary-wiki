<?php

namespace App\Validators;

use Illuminate\Validation\Validator as ValidatorInstance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\MessageBag;
use esperecyan\dictionary_php\{Validator as DictionaryValidator, exception\SyntaxException};
use Psr\Log\{AbstractLogger, LogLevel};

class DictionaryFileValidator extends AbstractLogger
{
    /** @var string[] */
    protected $messages;
    
    /** @inheritDoc */
    public function log($level, $message, array $context = [])
    {
        $this->messages[] = "$level: $message";
    }

    /**
     * 「主に単語で答えるゲームにおける汎用的な辞書形式」(汎用辞書) に同梱されるファイルのバリデート・矯正を行います。
     *
     *     dictionary_file
     *
     * @see https://github.com/esperecyan/dictionary/blob/master/dictionary.md#user-content-with-image-audio-video
     * @param string $attribute
     * @param \Illuminate\Http\UploadedFile $value
     * @param string[] $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function validate(
        string $attribute,
        UploadedFile $value,
        array $parameters,
        ValidatorInstance $validator
    ): bool {
        $this->messages = [];
        
        $dictionaryValidator = new DictionaryValidator();
        $dictionaryValidator->setLogger($this);
        
        try {
            $file = $dictionaryValidator->correct($value, $value->getClientOriginalName());
        } catch (SyntaxException $exception) {
            $validator->errors()->add($attribute, LogLevel::CRITICAL . ': ' . $exception->getMessage());
            return false;
        }
        
        $value->openFile('w')->fwrite($file['bytes']);

        if ($this->messages) {
            // $validator->errors() が空でない場合バリデーション失敗扱いになるため、フラッシュデータに直接エラーを記録
            session()->flash('errors', session('errors', new MessageBag())->merge([$attribute => $this->messages]));
        }

        return true;
    }
}
