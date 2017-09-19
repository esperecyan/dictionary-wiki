<?php

namespace Tests\Unit;

use Tests\TestCase;
use GrahamCampbell\Markdown\Facades\Markdown;

class MarkdownTest extends TestCase
{
    /**
     * @param  string  $lml
     * @param  string  $html
     * @return void
     *
     * @dataProvider markupProvider
     */
    public function testConvertToHtml(string $lml, string $html): void
    {
        $this->assertSame($html, trim(Markdown::convertToHtml($lml)));
    }
    
    public function markupProvider(): array
    {
        return [
            [
                '[UTF-8](https://game.pokemori.jp/dictionary-wiki/dictionaries?search=%E3%83%86%E3%82%B9%E3%83%88)',
                '<p><a href="https://game.pokemori.jp/dictionary-wiki/dictionaries?search=%E3%83%86%E3%82%B9%E3%83%88">UTF-8</a></p>',
            ],
            [
                '[EUC-JP](http://wikiwiki.jp/?WIKIWIKI.jp%2A%A4%CE%C6%C3%C4%A7)',
                '<p><a href="http://wikiwiki.jp/?WIKIWIKI.jp%2A%A4%CE%C6%C3%C4%A7">EUC-JP</a></p>',
            ],
        ];
    }
}
