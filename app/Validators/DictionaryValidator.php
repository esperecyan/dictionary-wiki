<?php

namespace App\Validators;

use Illuminate\Validation\Validator as ValidatorInstance;
use esperecyan\dictionary_php\{Parser, exception\SyntaxException};
use SplTempFileObject;
use Psr\Log\{AbstractLogger, LogLevel};

class DictionaryValidator extends AbstractLogger
{
    /** @var string[] */
    protected $messages;
    
    /**
     * LogLevel::Error以上に深刻度が高いロギングが発生していれば真。
     *
     * @var bool
     */
    protected $errorOrHigherOccurred = false;
    
    /** @inheritDoc */
    public function log($level, $message, array $context = [])
    {
        if (!$this->errorOrHigherOccurred
            && in_array($level, [LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY])) {
            $this->errorOrHigherOccurred = true;
        }
        $this->messages[] = "$level: $message";
    }

    /**
     * 「主に単語で答えるゲームにおける汎用的な辞書形式」(汎用辞書) のバリデートを行います。
     *
     *     dictionary:同梱されるファイル名1,同梱されるファイル名2,……
     *
     * @see https://github.com/esperecyan/dictionary/blob/master/dictionary.md
     * @param string $attribute
     * @param string $value
     * @param string[] $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function validate(
        string $attribute,
        string $value,
        array $parameters,
        ValidatorInstance $validator
    ): bool {
        $this->messages = [];
        
        $parser = new Parser('汎用辞書');
        $parser->setLogger($this);
        
        $file = new SplTempFileObject();
        $file->fwrite($value);
        try {
            $parser->parse($file, true, $parameters);
        } catch (SyntaxException $exception) {
            $this->errorOrHigherOccurred = true;
            $this->messages = [LogLevel::CRITICAL . ': ' . $exception->getMessage()];
        }
        
        if ($this->errorOrHigherOccurred) {
            $validator->errors()->merge([$attribute => $this->messages]);
            return false;
        }

        return true;
    }
}
