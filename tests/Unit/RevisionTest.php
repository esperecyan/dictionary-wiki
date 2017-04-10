<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Revision;
use Illuminate\Support\HtmlString;

class RevisionTest extends TestCase
{
    /**
     * @param  string[][]  $old
     * @param  string[][]  $new
     * @param  string[][]  $diff
     * @return void
     *
     * @dataProvider diffTablesProvider
     */
    public function testDiffTables(array $old, array $new, array $diff): void
    {
        $this->assertEquals($diff, Revision::diffTables($old, $new));
    }
    
    public function diffTablesProvider(): array
    {
        return [
            [
                [
                    ['text'  , 'answer'  ],
                    ['太陽'  , 'たいよう'],
                    ['地球'  , 'ちきゅう'],
                    ['カロン', ''        ],
                ],
                [
                    ['text'  , 'answer'  , 'answer'  ],
                    ['太陽'  , 'たいよう', 'おひさま'],
                    ['地球'  , 'ちきゅう', ''        ],
                    ['カロン', ''        , ''        ],
                ],
                [
                    ['!'  , ''      , ''        , '+++'     ],
                    ['@@' , 'text'  , 'answer'  , 'answer'  ],
                    ['+'  , '太陽'  , 'たいよう', 'おひさま'],
                    [''   , '地球'  , 'ちきゅう', ''        ],
                    ['...', '...'   , '...'     , '...'     ],
                ],
            ],
            [
                [
                    ['text'  , 'answer'  , 'answer'  ],
                    ['太陽'  , 'たいよう', 'おひさま'],
                    ['地球'  , 'ちきゅう', ''        ],
                ],
                [
                    ['text'  , 'answer'  , 'answer'  ],
                    ['太陽'  , 'たいよう', 'おひさま'],
                    ['地球'  , 'ちきゅう', ''        ],
                    ['カロン', ''        , ''        ],
                ],
                [
                    ['@@' , 'text'  , 'answer'  , 'answer'  ],
                    ['...', '...'   , '...'     , '...'     ],
                    [''   , '地球'  , 'ちきゅう', ''        ],
                    ['+++', 'カロン', ''        , ''        ],
                ],
            ],
            [
                [
                    ['text'  , 'answer'  , 'answer'  ],
                    ['太陽'  , 'たいよう', 'おひさま'],
                    ['地球'  , 'ちきゅう', ''        ],
                    ['かろん', ''        , ''        ],
                ],
                [
                    ['text'  , 'answer'  , 'answer'  ],
                    ['太陽'  , 'たいよう', 'おひさま'],
                    ['地球'  , 'ちきゅう', ''        ],
                    ['カロン', ''        , ''        ],
                ],
                [
                    ['@@' , 'text'          , 'answer'  , 'answer'  ],
                    ['...', '...'           , '...'     , '...'     ],
                    [''   , '地球'          , 'ちきゅう', ''        ],
                    ['->' , 'かろん->カロン', ''        , ''        ],
                ],
            ],
        ];
    }
    
    /**
     * @param  string  $data
     * @param  string[][]  $csv
     * @param  (string|\Illuminate\Support\HtmlString)[][]  $records
     * @return void
     *
     * @dataProvider recordsProvider
     */
    public function testGetCsvAttribute(string $data, array $csv, array $records): void
    {
        $this->assertEquals($csv, factory(Revision::class)->make(['data' => $data])->csv);
    }
    
    /**
     * @param  string  $data
     * @param  string[][]  $csv
     * @param  (string|\Illuminate\Support\HtmlString)[][]  $records
     * @return void
     *
     * @dataProvider recordsProvider
     */
    public function testGetRecordsAttribute(string $data, array $csv, array $records): void
    {
        $this->assertEquals($records, factory(Revision::class)->make(['data' => $data])->records);
    }
    
    public function recordsProvider(): array
    {
        return [
            [
                "text,answer,answer,description,@title,@summary\r
太陽,たいよう,おひさま,恒星。,天体,恒星、惑星、衛星などのリスト。\r
地球,ちきゅう,,惑星。,,\r
カロン,,,\"冥王星の衛星。\r
\r
> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。\r
> その後、冥王星が冥府の王プルートーの名に因むことから、\r
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。\r
> なおクリスティーは当初から一貫してCharonの「char」を\r
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、\r
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。\r
\r
引用元: [カロン (衛星) — Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))\",,\r
",
                [
                    ['text'  , 'answer'  , 'answer'  , 'description', '@title', '@summary'                      ],
                    ['太陽'  , 'たいよう', 'おひさま', '恒星。'     , '天体'  , '恒星、惑星、衛星などのリスト。'],
                    ['地球'  , 'ちきゅう', ''        , '惑星。'     , ''      , ''                              ],
                    ['カロン', ''        , ''        , '冥王星の衛星。

> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
> その後、冥王星が冥府の王プルートーの名に因むことから、
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
> なおクリスティーは当初から一貫してCharonの「char」を
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。

引用元: [カロン (衛星) — Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))', '', ''],
                ],
                [
                    ['text'  , 'answer'  , 'answer'  , 'description'                    ],
                    ['太陽'  , 'たいよう', 'おひさま', new HtmlString("<p>恒星。</p>\n")],
                    ['地球'  , 'ちきゅう', ''        , new HtmlString("<p>惑星。</p>\n")],
                    ['カロン', ''        , ''        , new HtmlString('<p>冥王星の衛星。</p>
<blockquote>
<p>カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
その後、冥王星が冥府の王プルートーの名に因むことから、
この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
なおクリスティーは当初から一貫してCharonの「char」を
妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
これが英語圏で定着して「シャーロン」と呼ばれるようになった。</p>
</blockquote>
<p>引用元: <a href="https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F)">カロン (衛星) — Wikipedia</a></p>
')],
                ],
            ],
            
        ];
    }
}
