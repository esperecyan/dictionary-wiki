<?php

namespace Tests\Unit\Validators;

use esperecyan\webidl\TypeError;
use esperecyan\url\URL;
use Tests\TestCase;
use Validator;

class MarkdownValidatorTest extends TestCase
{
    /**
     * @var (string|(string|callable|string[])[])[]
     */
    const WHITELIST = [
        'a' => ['href' => self::class . '::isAbsoluteURLWithHTTPScheme'], 'blockquote', 'br', 'code', 'em',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'li', 'ol' => ['start' => '/^(?:0|-?[1-9][0-9]*)$/u'],
        'p', 'pre', 'q' => ['cite' => self::class . '::isAbsoluteURLWithHTTPScheme'], 'strong', 'ul',
    ];
    
    /**
     * HTTP(S)スキームをもつ絶対URLであれば真を返します。
     *
     * @param string $value
     * @return bool
     */
    public static function isAbsoluteURLWithHTTPScheme(string $value): bool
    {
        try {
            $url = new URL($value);
        } catch (TypeError $exception) {
            return false;
        }
        return in_array($url->protocol, ['http:', 'https:']);
    }
    
    /**
     * @param  string  $lml
     * @param  string[]  $errors
     * @return void
     *
     * @dataProvider lmlProvider
     */
    public function testValidate(string $lml, array $errors): void
    {
        $validator = Validator::make(['profile' => $lml], ['profile' => 'markdown:' . static::class . '::WHITELIST']);
        if ($errors) {
            $this->assertEquals(array_merge($errors, ['validation.markdown']), $validator->errors()->get('profile'));
        } else {
            $this->assertTrue($validator->passes(), print_r($validator->errors()->get('profile'), true));
        }
    }
    
    public function lmlProvider(): array
    {
        return [
            ['[HTTPSリンク](https://url.test/)', []],
            ['[HTTPリンク](http://url.test/)', []],
            ['[FTPリンク](ftp://url.test/)', ['<a> タグの href 属性値 "ftp://url.test/" は許可されていません。']],
            ['<a href="https://url.test/">HTTPSリンク</a>', []],
            ['<a href="http://url.test/">HTTPリンク</a>', []],
            ['<a href="ftp://url.test/">FTPリンク</a>', ['<a> タグの href 属性値 "ftp://url.test/" は許可されていません。']],
            ['<https://url.test/>', []],
            ['<http://url.test/>', []],
            ['<ftp://url.test/>', ['<a> タグの href 属性値 "ftp://url.test/" は許可されていません。']],
            ['ftp://url.test/', []],
        ];
    }
}
