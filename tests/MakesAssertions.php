<?php

namespace Tests;

use Masterminds\HTML5;
use DOMAttr;

trait MakesAssertions
{
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
        [$expected, $actual] = array_map(function (string $htmlString): string {
            $html5 = new HTML5();
            $fragment = $html5->loadHTMLFragment($htmlString);
            $nodes = iterator_to_array($fragment->childNodes);
            while ($nodes) {
                $node = array_shift($nodes);
                switch ($node->nodeType) {
                    case XML_ELEMENT_NODE:
                        $attributes = array_map(function (DOMAttr $attributeNode): string {
                            return $attributeNode->value;
                        }, iterator_to_array($node->attributes));
                        foreach ($attributes as $name => $value) {
                            $node->removeAttribute($name);
                        }
                        ksort($attributes, SORT_STRING);
                        foreach ($attributes as $name => $value) {
                            $node->setAttribute($name, $value);
                        }
                        $nodes = array_merge($nodes, iterator_to_array($node->childNodes));
                        break;
                    case XML_TEXT_NODE:
                        $data = trim($node->data);
                        if ($data === '') {
                            $node->parentNode->removeChild($node);
                        } else {
                            $node->data = $data;
                        }
                        break;
                }
            }
            $doc = $fragment->ownerDocument;
            $doc->formatOutput = true;
            return $html5->saveHTML($html5->loadHTMLFragment($doc->saveXML($fragment)));
        }, [$expected, $actual]);
        $this->assertSame($expected, $actual, $message);
    }
}
