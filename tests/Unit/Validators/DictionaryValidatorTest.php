<?php

namespace Tests\Unit\Validators;

use Tests\TestCase;
use Validator;

class DictionaryValidatorTest extends TestCase
{
    /**
     * @param  string  $csv
     * @param  string[]  $filenames
     * @param  array  $errors
     * @return void
     *
     * @dataProvider dictionaryProvider
     */
    public function testValidate(string $csv, array $filenames, array $errors): void
    {
        $validator = Validator::make(
            ['csv' => $csv],
            ['csv' => 'dictionary' . ($filenames ? ':' . implode(',', $filenames) : '')],
            ['dictionary' => '「error」と「critical」を修正する必要があります。']
        );
        if ($errors) {
            $this->assertEquals(
                array_merge($errors, ['「error」と「critical」を修正する必要があります。']),
                $validator->errors()->get('csv')
            );
        } else {
            $this->assertTrue($validator->passes(), print_r($validator->errors()->get('csv'), true));
        }
    }
    
    public function dictionaryProvider(): array
    {
        return [
            ['text,image,answer,answer,description,@title,@summary
太陽,sun.png,たいよう,おひさま,恒星。,天体,恒星、惑星、衛星などのリスト。
地球,earth.png,,ちきゅう,惑星。,,
カロン,charon.png,,,"冥王星の衛星。

> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
> その後、冥王星が冥府の王プルートーの名に因むことから、
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
> なおクリスティーは当初から一貫してCharonの「char」を
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。
引用元: [カロン (衛星) - Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))",,
', ['sun.png', 'earth.png', 'charon.png'], []],
            ['text,image,answer,answer,description,@title,@summary
太陽,sun.png,たいよう,おひさま,恒星。,天体,恒星、惑星、衛星などのリスト。
地球,earth.png,,ちきゅう,惑星。,,
カロン,charon.png,,,"冥王星の衛星。

> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
> その後、冥王星が冥府の王プルートーの名に因むことから、
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
> なおクリスティーは当初から一貫してCharonの「char」を
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。
引用元: [カロン (衛星) - Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))",,
', [], [
                'error: 「sun.png」はファイル所在の規則に合致しません。',
                'error: 「earth.png」はファイル所在の規則に合致しません。',
                'error: 「charon.png」はファイル所在の規則に合致しません。',
            ]],
            ['text,image,answer,answer,description,@title,@summary
太陽,sun.png,たいよう,おひさま,恒星。,天体,恒星、惑星、衛星などのリスト。
地球,earth.png,,ちきゅう,惑星。,,
カロン,charon.png,,,"冥王星の衛星。

> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
> その後、冥王星が冥府の王プルートーの名に因むことから、
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
> なおクリスティーは当初から一貫してCharonの「char」を
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。
引用元: [カロン (衛星) - Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))",,
', [], [
                'error: 「sun.png」はファイル所在の規則に合致しません。',
                'error: 「earth.png」はファイル所在の規則に合致しません。',
                'error: 「charon.png」はファイル所在の規則に合致しません。',
            ]],
            ['太陽,たいよう,おひさま
地球,ちきゅう
カロン
', [], [
                'critical: ヘッダ行「太陽,たいよう,おひさま」にフィールド名「text」が存在しません',
            ]],
            ['text,image,image-source,description
テスト,https://resource.test/image.png,"# 見出し1
本文

見出し2
=======
本文

見出し3
-------
[リンク] **強調** <b>名前</b> _強勢_ <i style=""font-weight: bold;"">心の声</i> `コード`

[リンク]: https://example.jp/","# 見出し1
本文

見出し2
=======
本文

見出し3
-------
[リンク] **強調** <b>名前</b> _強勢_ <i style=""font-weight: bold;"">心の声</i> `コード`

[リンク]: https://example.jp/"
', [], [
                'error: 「# 見出し1
本文

見出し2
====…」に次のエラーが出ています:
• <h1> タグの使用は許可されていません。
• <h1> タグの使用は許可されていません。
• <h2> タグの使用は許可されていません。
• <strong> タグの使用は許可されていません。
• <em> タグの使用は許可されていません。
• <i> タグの style 属性の使用は許可されていません。
• <code> タグの使用は許可されていません。',
                'error: 「# 見出し1
本文

見出し2
====…」に次のエラーが出ています:
• <i> タグの style 属性の使用は許可されていません。',
            ]],
            ['text,image,audio,video,specifics
ピン,png.png,,,
ジェイフィフ,jfif.jpg,,,
エスブイジー,svg.svg,,,
エーエーシー,,mpeg4-aac.m4a,,
エムピースリー,,mpeg1-audio-layer3.mp3,,
エイチニーロクヨン,,,mpeg4-h264.mp4,
ウェブエム,,,webm-vb8.webm,score=1.0E%2B15
', ['png.png', 'jfif.jpg', 'svg.svg', 'mpeg4-aac.m4a', 'mpeg1-audio-layer3.mp3', 'mpeg4-h264.mp4'], [
                'error: 「webm-vb8.webm」はファイル所在の規則に合致しません。',
                'error: 「1.0E+15」は整数の規則に合致しません。',
            ]],
            ['<?xml version="1.0" ?>
            <svg xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" /></svg>', [], [
                'critical: 汎用辞書はCSVファイルかZIPファイルでなければなりません。',
            ]],
            ['text,description
おだい,説明,ヘッダ行よりフィールド数が多いレコード
', [], [
                'critical: 「おだい,説明,ヘッダ行よりフィールド数が多いレコード」のフィールド数は、ヘッダ行のフィールド名の数を超えています。',
            ]],
            ['text
テスト', array_map(function ($index) {
    return "$index.png";
}, range(1, 10000)), [
                'critical: アーカイブ中のファイル数は 10000 個以下にしてください: 現在 10001 個',
            ]],
        ];
    }
}
