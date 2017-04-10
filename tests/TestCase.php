<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Masterminds\HTML5;
use DOMDocument;
use DOMXPath;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, GeneratesTempFile;
    
    /**
     * テキストノード中の空白、および空のテキストノードを取り除き、2つのHTML文字列が一致することを確認します。
     *
     * @param  string  $expected
     * @param  string  $actual
     * @param  string  $message
     * @return void
     */
    protected function assertEqualHTMLStringWithoutWhiteSpaces(
        string $expected,
        string $actual,
        string $message = ''
    ): void {
        [$expected, $actual] = array_map(function (string $htmlString): DOMDocument {
            $doc = (new HTML5())->loadHTML('<title></title>' . $htmlString);
            foreach ((new DOMXPath($doc))->query('//text()') as $text) {
                $data = trim($text->data);
                if ($data === '') {
                    $text->parentNode->removeChild($text);
                } else {
                    $text->data = $data;
                }
            }
            return $doc;
        }, [$expected, $actual]);
        $this->assertEquals($expected, $actual, $message);
    }
}
