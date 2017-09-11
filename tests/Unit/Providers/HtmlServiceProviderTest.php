<?php

namespace Tests\Unit\Providers;

use Tests\TestCase;
use Html;
use Form;
use Illuminate\Support\HtmlString;

class HtmlServiceProviderTest extends TestCase
{
    /**
     * @param  string|\Illuminate\Support\HtmlString  $value
     * @param  string|\Illuminate\Support\HtmlString  $html
     * @return void
     *
     * @dataProvider htmlProvider
     */
    public function testEntities($value, $html): void
    {
        $this->assertEquals($html, Html::entities($value));
    }
    
    public function htmlProvider(): array
    {
        return [
            [
                'テスト <https://dictionary-wiki.test/>',
                'テスト &lt;https://dictionary-wiki.test/&gt;',
            ],
            [
                new HtmlString('<a href="https://dictionary-wiki.test/">テスト</a>'),
                new HtmlString('<a href="https://dictionary-wiki.test/">テスト</a>'),
            ],
        ];
    }
    
    /**
     * @param  array  $options
     * @param  string  $form
     * @return void
     *
     * @dataProvider formProvider
     */
    public function testOpen(array $options, string $form): void
    {
        $this->assertRegExp(str_replace('{{ $appURL }}', url(''), $form), Form::open($options)->toHtml());
    }
    
    public function formProvider(): array
    {
        return [
            [
                ['method' => 'GET', 'route' => 'dictionaries.index'],
                '#^<form method="GET" action="{{ $appURL }}/dictionaries" accept-charset="UTF-8">$#u',
            ],
            [
                ['method' => 'POST', 'route' => 'dictionaries.create'],
                '#^<form method="POST" action="{{ $appURL }}/dictionaries/create" accept-charset="UTF-8"><input name="_token" type="hidden"(?: value="[^"]+")?>$#u',
            ],
            [
                ['method' => 'POST', 'route' => 'dictionaries.create', 'files' => true],
                '#^<form method="POST" action="{{ $appURL }}/dictionaries/create" accept-charset="UTF-8" enctype="multipart/form-data"><input name="_token" type="hidden"(?: value="[^"]+")?><input name="MAX_FILE_SIZE" type="hidden" value="[0-9]+">$#u',
            ],
        ];
    }
    
    /**
     * @param  string  $type
     * @param  string  $name
     * @param  string|null  $value
     * @param  array  $options
     * @param  string  $input
     * @return void
     *
     * @dataProvider inputProvider
     */
    public function testInput(string $type, string $name, ?string $value, array $options, string $input): void
    {
        $this->assertEqualHTMLStringWithoutWhiteSpaces($input, Form::input($type, $name, $value, $options)->toHtml());
    }
    
    public function inputProvider(): array
    {
        return [
            'Hidden' => ['hidden', 'type', 'upload', [], '
                <input name="type" value="upload" type="hidden" />
            '],
            'Text' => ['text', 'name', null, [], '
                <input name="name" type="text" />
                <div class="help-block with-errors"><ul></ul></div>
            '],
            'Search' => ['search', 'name', null, [], '
                <input name="name" type="search" />
                <div class="help-block with-errors"><ul></ul></div>
            '],
            'Chackbox' => ['checkbox', 'delete', null, [], '
                <input name="delete" type="checkbox" />
            '],
            'Radio' => ['radio', 'locale', 'ja', [], '
                <input name="locale" value="ja" type="radio">
            '],
            'Submit' => ['submit', 'upload', null, [], '
                <input name="upload" type="submit" />
            '],
            'Reset' => ['reset', 'reset', null, [], '
                <input name="reset" type="reset" />
            '],
            'Button' => ['button', 'copy', null, [], '
                <input name="copy" type="button" />
            '],
            '必須のText' => ['text', 'name', null, ['required' => ''], '
                <input name="name" required="" type="text" />
                <div class="help-block with-errors"><ul></ul></div>
                <span class="label label-primary required">必須</span>
            '],
            '必須のSearch' => ['search', 'name', null, ['required' => ''], '
                <input name="name" required="" type="search" />
                <div class="help-block with-errors"><ul></ul></div>
                <span class="label label-primary required">必須</span>
            '],
            '必須のChackbox' => ['checkbox', 'delete', null, ['required' => ''], '
                <input name="delete" required="" type="checkbox" />
                <span class="label label-primary required">必須</span>
            '],
            '必須のRadio' => ['radio', 'locale', 'ja', ['required' => ''], '
                <input name="locale" value="ja" required="" type="radio" />
            '],
        ];
    }
    
    /**
     * @param  string  $name
     * @param  ?string  $value
     * @param  array  $options
     * @param  string  $textarea
     * @return void
     *
     * @dataProvider textareaProvider
     */
    public function testTextarea(string $name, ?string $value, array $options, string $textarea): void
    {
        $this->assertEqualHTMLStringWithoutWhiteSpaces($textarea, Form::textarea($name, $value, $options)->toHtml());
    }
    
    public function textareaProvider(): array
    {
        return [
            ['data', null, [], '
                <textarea name="data" cols="50" rows="10"></textarea>
                <div class="help-block with-errors"><ul></ul></div>
            '],
            ['data', null, ['required' => ''], '
                <textarea name="data" required="" cols="50" rows="10"></textarea>
                <div class="help-block with-errors"><ul></ul></div>
                <span class="label label-primary required">必須</span>
            '],
        ];
    }
    
    /**
     * @param  string  $name
     * @param  array  $list
     * @param  ?string  $selected
     * @param  array  $options
     * @param  string  $select
     * @return void
     *
     * @dataProvider selectProvider
     */
    public function testSelect(string $name, array $list, ?string $selected, array $options, string $select): void
    {
        $this->assertEqualHTMLStringWithoutWhiteSpaces(
            $select,
            Form::select($name, $list, $selected, $options)->toHtml()
        );
    }
    
    public function selectProvider(): array
    {
        return [
            ['category', ['public' => '公開', 'private' => '個人用'], 'private', [], '
                <select name="category">
                    <option value="public">公開</option>
                    <option value="private" selected="selected">個人用</option>
                </select>
                <div class="help-block with-errors"><ul></ul></div>
            '],
            ['category', ['public' => '公開', 'private' => '個人用'], null, ['placeholder' => '選択してください'], '
                <select name="category">
                    <option selected="selected" disabled="" value="">選択してください</option>
                    <option value="public">公開</option>
                    <option value="private">個人用</option>
                </select>
                <div class="help-block with-errors"><ul></ul></div>
            '],
        ];
    }
}
