<?php

namespace Tests\Unit\Providers;

use Tests\TestCase;
use App\Dictionary;
use Html;
use Illuminate\Support\HtmlString;

class MacroServiceProviderTest extends TestCase
{
    /**
     * @param  array  $attributes
     * @param  string  $warnings
     * @return void
     *
     * @dataProvider dictionaryProvider
     */
    public function testShowDictionaryWarnings(array $attributes, string $warnings): void
    {
        $this->assertEqualHTMLStringWithoutWhiteSpaces(
            $warnings,
            Html::showDictionaryWarnings(factory(Dictionary::class)->make($attributes))
        );
    }
    
    public function dictionaryProvider(): array
    {
        return [
            [['regard' => null], ''],
            [['regard' => '[a-z]'], '
                <i class="fa fa-exclamation-triangle"></i>
                <span class="message-icon" title="ひらがな (カタカナ) 以外が答えに含まれるお題があります">
                    <i class="fa fa-language"></i>
                    <span class="text-hide">ひらがな (カタカナ) 以外が答えに含まれるお題があります</span>
                </span>
            '],
        ];
    }
    
    /**
     * @param  string  $lml
     * @param  string  $html
     * @return void
     *
     * @dataProvider fieldProvider
     */
    public function testConvertField(string $lml, string $html): void
    {
        $this->assertEqualHTMLStringWithoutWhiteSpaces($html, Html::convertField($lml));
    }
    
    public function fieldProvider(): array
    {
        return [
            ['テスト', '<p>テスト</p>'],
            ['冥王星の衛星。

> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
> その後、冥王星が冥府の王プルートーの名に因むことから、
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
> なおクリスティーは当初から一貫してCharonの「char」を
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。
引用元: [カロン (衛星) - Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))', '
                <p>冥王星の衛星。</p>
                <blockquote>
                <p>
カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
その後、冥王星が冥府の王プルートーの名に因むことから、
この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
なおクリスティーは当初から一貫してCharonの「char」を
妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
これが英語圏で定着して「シャーロン」と呼ばれるようになった。
引用元: <a href="https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F)">カロン (衛星) - Wikipedia</a>
                </p>
                </blockquote>
            '],
            ['![ピン](https://resource.test/png.png) <audio src="tag:resource.test,2016:mp4.m4a"></audio> <video src="urn:uuid:bd111782-5b46-426e-b0ec-4a47d4f7e577"></video>', '<p>![ピン](https://resource.test/png.png) &lt;audio src="tag:resource.test,2016:mp4.m4a"&gt;&lt;/audio&gt; &lt;video src="urn:uuid:bd111782-5b46-426e-b0ec-4a47d4f7e577"&gt;&lt;/video&gt;</p>'],
        ];
    }
}
