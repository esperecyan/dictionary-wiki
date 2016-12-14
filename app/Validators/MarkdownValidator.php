<?php

namespace App\Validators;

use Illuminate\Validation\Validator as ValidatorInstance;
use Markdown;
use esperecyan\html_filter\Filter;
use Psr\Log\AbstractLogger;

class MarkdownValidator extends AbstractLogger
{
    /** @var string[] */
    protected $messages;
    
    /** @inheritDoc */
    public function log($level, $message, array $context = [])
    {
        $this->messages[] = $message;
    }

    /**
     * CommonMarkdown文字列のバリデートを行います。
     *
     *     markdown:ホワイトリスト配列の定数名
     *
     * ホワイトリスト配列の定数名は constant() 関数で解決可能な文字列です。
     * ホワイトリスト配列の構造については[esperecyan/html-filter]のREADMEを参照してください。
     *
     * [esperecyan/html-filter]: https://github.com/esperecyan/html-filter "ホワイトリストによる要素名と属性名のチェックを行うシンプルなHTMLフィルターです。"
     *
     * @param string $attribute
     * @param string $value
     * @param string[] $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function validate(string $attribute, string $value, array $parameters, ValidatorInstance $validator): bool
    {
        $this->messages = [];
        
        $filter = new Filter(constant($parameters[0]));
        $filter->setLogger($this);
        $filter->filter(Markdown::convertToHtml($value));

        if ($this->messages) {
            $validator->errors()->merge([$attribute => $this->messages]);
            return false;
        }

        return true;
    }
}
