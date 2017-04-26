<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Facebook\WebDriver\WebDriverKeys as Keys;
use finfo;

class DictionariesTest extends DuskTestCase
{
    /**
     * @return void
     */
    public function testIndex(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/dictionaries')
                ->assertSeeLink('辞書名')
                ->assertSeeLink('語数')
                ->assertSeeLink('更新日時')->assertVisible('thead th:nth-of-type(4) .fa-sort-numeric-desc');
        });
    }
    
    /**
     * 辞書の作成。
     *
     * @see https://github.com/esperecyan/dictionary/blob/master/dictionary.md#including-images-descriptions
     * @return string
     */
    public function testStore(): string
    {
        $url;
        $this->browse(function (Browser $browser) use (&$url) {
            $browser->loginAs(1)->visit('/dictionaries/create')
                ->assertTitle('辞書の新規作成 | 辞書まとめwiki β版')
                ->type('tags', "test\nテスト")
                ->select('category', 'generic')
                ->assertInputValue('locale', 'ja')
                ->radio('uploading', '0')
            ->press('新規作成');
                $this->assertSame(config('app.url') . '/dictionaries/create', $browser->driver->getCurrentURL());
                $browser->assertSee('critical: 「,,,[辞書名],《辞書の概要》」にはtextフィールドが存在しません。')
                    ->assertSee('「error」と「critical」を修正する必要があります。')
                
                ->rightClick('#table tbody tr td:nth-of-type(1)');
            foreach ($browser->elements('.htItemWrapper') as $item) {
                if (str_contains($item->getText(), '右に列を挿入')) {
                    $item->click();
                    break;
                }
            }
                $browser->rightClick('#table tbody tr td:nth-of-type(1)');
            foreach ($browser->elements('.htItemWrapper') as $item) {
                if (str_contains($item->getText(), '右に列を挿入')) {
                    $item->click();
                    break;
                }
            }
            
                $browser->click('#table tbody tr:first-of-type td:nth-of-type(2)');
                $remoteTargetLocator = $browser->driver->switchTo();
                $remoteTargetLocator->activeElement()->sendKeys('image')
                    ->sendKeys(Keys::TAB)
                    ->sendKeys('answer')
                    ->sendKeys(Keys::ARROW_RIGHT)->sendKeys(Keys::ARROW_RIGHT)->sendKeys(Keys::ARROW_RIGHT)
                    ->sendKeys(Keys::ARROW_DOWN)
                    ->sendKeys('天体')
                    ->sendKeys(Keys::TAB)->sendKeys('恒星、惑星、衛星などのリスト。')
                    ->sendKeys([Keys::SHIFT, Keys::TAB])->sendKeys([Keys::SHIFT, Keys::TAB])
                        ->sendKeys([Keys::SHIFT, Keys::TAB])->sendKeys([Keys::SHIFT, Keys::TAB])
                        ->sendKeys([Keys::SHIFT, Keys::TAB])->sendKeys([Keys::SHIFT, Keys::TAB])
                ->sendKeys('太陽')
                    ->sendKeys(Keys::TAB)//->sendKeys('sun.png')
                    ->sendKeys(Keys::TAB)->sendKeys('たいよう')
                    ->sendKeys(Keys::TAB)->sendKeys('おひさま')
                    ->sendKeys(Keys::TAB)->sendKeys('恒星。')
                    ->sendKeys(Keys::ARROW_LEFT)->sendKeys(Keys::ARROW_LEFT)->sendKeys(Keys::ARROW_LEFT)
                        ->sendKeys(Keys::ARROW_LEFT)->sendKeys(Keys::ARROW_DOWN)
                ->sendKeys('地球')
                    ->sendKeys(Keys::TAB)//->sendKeys('earth.png')
                    ->sendKeys(Keys::TAB)->sendKeys('ちきゅう')
                    ->sendKeys(Keys::TAB)
                    ->sendKeys(Keys::TAB)->sendKeys('惑星。')
                    ->sendKeys(Keys::ARROW_LEFT)->sendKeys(Keys::ARROW_LEFT)->sendKeys(Keys::ARROW_LEFT)
                        ->sendKeys(Keys::ARROW_LEFT)->sendKeys(Keys::ARROW_DOWN)
                ->sendKeys('カロン')
                    ->sendKeys(Keys::TAB)->sendKeys('charon.png')
                    ->sendKeys(Keys::TAB)
                    ->sendKeys(Keys::TAB)
                    ->sendKeys(Keys::TAB)->sendKeys('冥王星の衛星。');
                        $remoteTargetLocator->activeElement()->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。')
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('> その後、冥王星が冥府の王プルートーの名に因むことから、')
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。')
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('> なおクリスティーは当初から一貫してCharonの「char」を')
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、')
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('> これが英語圏で定着して「シャーロン」と呼ばれるようになった。')
                        ->sendKeys([Keys::CONTROL, Keys::ENTER])
                        ->sendKeys('引用元: [カロン (衛星) — Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))');
                $browser->attach('added-files[]', $this->generateTempFile($this->generateImage(), 'charon.png'))
            ->press('新規作成')->press('新規作成'); // Laravel Duskでは二重にpressが必要 (Handsontableの影響？)
                $this->assertStringMatchesFormat(
                    config('app.url') . '/dictionaries/%d',
                    $browser->driver->getCurrentURL()
                );
                $browser->assertTitle('天体 | 辞書まとめwiki β版')
                ->assertSee('成功しました。');
            
            $url = $browser->driver->getCurrentURL();
            $browser->logout();
        });
        
        return $url;
    }
    
    /**
     * アップロードによる辞書の作成。
     *
     * @return void
     */
    public function testStoreDictionaryFile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)->visit('/dictionaries/create')
                ->assertTitle('辞書の新規作成 | 辞書まとめwiki β版')
                ->select('category', 'specific')
                ->attach('dictionary', base_path('tests/dictionaries/touhou-characters.csv'))
            ->press('新規作成')
                ->assertTitle('東方Project 登場人物 紅魔郷以降の主要キャラ | 辞書まとめwiki β版');
                $this->assertEqualHTMLStringWithoutWhiteSpaces(
                    '<h1></h1>
                    <ul>
                        <li>
                            上海アリス幻樂団によるゲーム作品 (弾幕シューティング)
                            <ul>
                                <li>主人公</li>
                                <li>ボス</li>
                                <li>
                                    中ボス
                                    <ul>
                                        <li>
                                            紅魔郷二面中ボスは
                                            <a href="http://dic.pixiv.net/a/%E5%A4%A7%E5%A6%96%E7%B2%BE#h2_0">
                                                <code>だいようせい</code>
                                            </a>
                                        </li>
                                        <li>
                                            紅魔郷四面中ボスは
                                            <a href="http://dic.pixiv.net/a/%E5%B0%8F%E6%82%AA%E9%AD%94(%E6%9D%B1%E6%96%B9Project)">
                                                <code>こあくま</code>
                                            </a>
                                        </li>
                                        <li>
                                            <ruby>人形<rt>ひとがた</rt></ruby>
                                            でない
                                            <a href="http://dic.pixiv.net/a/%E7%A5%9E%E9%9C%8A%E5%BB%9F%E4%B8%80%E9%9D%A2%E4%B8%AD%E3%83%9C%E3%82%B9">
                                                神霊廟一面中ボス
                                            </a>
                                            は未収録
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            上海アリス幻樂団、黄昏フロンティアによるゲーム作品 (弾幕アクション)
                            <ul>
                                <li>プレイヤーキャラクター</li>
                            </ul>
                        </li>
                        <li>
                            書籍・CD
                            <ul>
                                <li>
                                    名前 (固有名称) と容姿が分かる<ruby>人形<rt>ひとがた</rt></ruby>の登場人物
                                    <ul>
                                        <li>あまり有名でない<a href="http://dic.pixiv.net/a/%E9%81%8B%E6%9D%BE">運松</a>は未収録</li>
                                    </ul>
                                </li>
                                <li>
                                    容姿が分かる有名な登場人物
                                    <ul>
                                        <li>
                                            <a href="http://dic.pixiv.net/a/%E6%9C%B1%E9%B7%BA%E5%AD%90">
                                                朱鷺子 (名無しの本読み妖怪)
                                            </a>
                                        </li>
                                        <li><a href="http://dic.pixiv.net/a/%E6%98%93%E8%80%85#h2_1">易者 (易者の妖怪)</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>',
                    $browser->element('main section:nth-of-type(2).list-group-item > section')
                        ->getAttribute('innerHTML')
                );
            $browser->logout();
        });
    }
    
    /**
     * PNG形式のバイナリ文字列を生成します。
     *
     * @return string
     */
    protected function generateImage(): string
    {
        $image = imagecreatetruecolor(1000, 1000);
        ob_start();
        imagepng($image);
        return ob_get_clean();
    }
    
    /**
     * 辞書の表示。
     *
     * @depends testStore
     * @param  string  $url
     * @return string
     */
    public function testShow(string $url): string
    {
        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit($url)
                ->assertTitle('天体 | 辞書まとめwiki β版')
                ->assertSeeLink('ダウンロード')->assertSeeIn('main .tabs .active', 'ダウンロード')
                ->assertSeeLink('お題一覧 3')
                ->assertSeeLink('更新履歴')
                ->assertSeeLink('編集')
                ->assertSeeLink('コメント欄')
                ->assertSee('一般・全般')
                ->assertSee('test')
                ->assertSee('テスト')
                ->assertSee('恒星、惑星、衛星などのリスト。');
            
            $urls = [];
            foreach ($browser->elements('a') as $anchor) {
                $urls[trim($anchor->getText())] = $anchor->getAttribute('href');
            }
            
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $this->assertSame('application/zip', $finfo->buffer(file_get_contents($urls['汎用辞書'])));
            $this->assertSame(
                "たいよう\t// 【太陽】恒星。\r\nちきゅう\t// 【地球】惑星。\r\nかろん\r\n",
                mb_convert_encoding(file_get_contents($urls['キャッチフィーリング']), 'UTF-8', 'Windows-31J')
            );
            $this->assertSame(
                "[天体]\r\n// 恒星、惑星、衛星などのリスト。\r\n\r\nたいよう,おひさま// 【太陽】恒星。\r\nちきゅう// 【地球】惑星。\r\nかろん\r\n",
                mb_convert_encoding(file_get_contents($urls['きゃっちま']), 'UTF-8', 'Windows-31J')
            );
            $this->assertSame('application/zip', $finfo->buffer(file_get_contents($urls['Inteligenceω クイズ'])));
            $this->assertSame(
                "% 【天体】\r\n% 恒星、惑星、衛星などのリスト。\r\n\r\n太陽,たいよう,おひさま,@恒星。\r\n地球,ちきゅう,@惑星。\r\nカロン,かろん,@冥王星の衛星。  > カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。 > その後、冥王星が冥府の王プルートーの名に因むことから、 > この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。 > なおクリスティーは当初から一貫してCharonの「char」を > 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、 > これが英語圏で定着して「シャーロン」と呼ばれるようになった。 引用元: [カロン (衛星) 〓 Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))\r\n",
                mb_convert_encoding(file_get_contents($urls['Inteligenceω しりとり']), 'UTF-8', 'Windows-31J')
            );
            $this->assertSame("たいよう\r\nちきゅう\r\nかろん\r\n", file_get_contents($urls['ピクトセンス']));
            $this->assertSame(str_replace("\n", "\r\n", 'text,image,answer,answer,description,@title,@summary
太陽,,たいよう,おひさま,恒星。,天体,恒星、惑星、衛星などのリスト。
地球,,ちきゅう,,惑星。,,
カロン,' . $url . '/files/charon.png,,,"冥王星の衛星。

> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。
> その後、冥王星が冥府の王プルートーの名に因むことから、
> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。
> なおクリスティーは当初から一貫してCharonの「char」を
> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、
> これが英語圏で定着して「シャーロン」と呼ばれるようになった。
引用元: [カロン (衛星) — Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))",,
'), file_get_contents($urls['汎用辞書 (CSVファイルのみ)']));
            $this->assertSame(
                "% 【天体】\r\n% 恒星、惑星、衛星などのリスト。\r\n\r\nQ,2,,$url/files/charon.png\r\nA,0,カロン,\\explain=カロン\\n\\n冥王星の衛星。\\n\\n> カロンは1978年6月22日にアメリカの天文学者ジェームズ・クリスティーによって発見された。\\n> その後、冥王星が冥府の王プルートーの名に因むことから、\\n> この衛星はギリシア神話の冥府の川・アケローンの渡し守カローンにちなんで「カロン」と命名された。\\n> なおクリスティーは当初から一貫してCharonの「char」を\\n> 妻シャーリーン（Charlene） のニックネーム「シャー（Char）」と同じように発音していたため、\\n> これが英語圏で定着して「シャーロン」と呼ばれるようになった。\\n引用元: [カロン (衛星) 〓 Wikipedia](https://ja.wikipedia.org/wiki/%E3%82%AB%E3%83%AD%E3%83%B3_(%E8%A1%9B%E6%98%9F))\r\n",
                mb_convert_encoding(file_get_contents($urls['Inteligenceω クイズ (テキストファイルのみ)']), 'UTF-8', 'Windows-31J')
            );
            
            $this->clearClipboardText($browser);
            $browser->pressAndWaitFor('Inteligenceω クイズの辞書のURLをクリップボードにコピー');
            $this->assertClipboardText($browser, "$url?type=quiz&scope=text");
            $browser->pressAndWaitFor('Inteligenceω しりとりの辞書のURLをクリップボードにコピー');
            $this->assertClipboardText($browser, "$url?type=siri");
            $browser->pressAndWaitFor('ピクトセンスの辞書の内容をクリップボードにコピー');
            $this->assertClipboardText($browser, "たいよう\nちきゅう\nかろん\n");
            $this->clearClipboardText($browser);
        });
        return $url;
    }
    
    /**
     * お題一覧。
     *
     * @depends testStore
     * @param  string  $url
     * @return void
     */
    public function testWords(string $url): void
    {
        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit("$url/words")
                ->assertTitle('天体 — お題一覧 | 辞書まとめwiki β版')
                ->assertSeeLink('ダウンロード')
                ->assertSeeLink('お題一覧 3')->assertSeeIn('main .tabs .active', 'お題一覧 3')
                ->assertSeeLink('更新履歴')
                ->assertSeeLink('編集')
                ->assertSeeLink('コメント欄')
                
                ->assertSeeIn('main thead th:nth-of-type(1)', 'text')
                ->assertSeeIn('main thead th:nth-of-type(2)', 'image')
                ->assertSeeIn('main thead th:nth-of-type(3)', 'answer')
                ->assertSeeIn('main thead th:nth-of-type(4)', 'answer')
                ->assertSeeIn('main thead th:nth-of-type(5)', 'description')
                ->assertDontSee('title')
                ->assertDontSee('summary')
                
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(1)', '太陽')
                //->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(2)', '')
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(3)', 'たいよう')
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(4)', 'おひさま')
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(5)', '恒星。')
                
                ->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(1)', '地球')
                //->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(2)', '')
                ->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(3)', 'ちきゅう')
                //->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(4)', '')
                ->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(5)', '惑星。')
                
                ->assertSeeIn('main tbody tr:nth-of-type(3) td:nth-of-type(1)', 'カロン')
                ->assertVisible("main tbody tr:nth-of-type(3) td:nth-of-type(2) img[src='$url/files/charon.png']")
                //->assertSeeIn('main tbody tr:nth-of-type(3) td:nth-of-type(3)', '')
                //->assertSeeIn('main tbody tr:nth-of-type(3) td:nth-of-type(4)', '')
                ->assertSeeIn('main tbody tr:nth-of-type(3) td:nth-of-type(5)', '冥王星の衛星。')
                    ->assertVisible('main tbody tr:nth-of-type(3) td:nth-of-type(5) blockquote')
                    ->assertSeeLink('カロン (衛星) — Wikipedia');
        });
    }
    
    /**
     * 辞書の編集。
     *
     * @depends testShow
     * @param  string  $url
     * @return string
     */
    public function testEdit(string $url): string
    {
        $this->browse(function (Browser $browser) use ($url) {
            $browser->loginAs(1)->visit("$url/edit")
                ->assertTitle('天体 — 編集 | 辞書まとめwiki β版')
                ->assertSeeLink('ダウンロード')
                ->assertSeeLink('お題一覧 3')
                ->assertSeeLink('更新履歴')
                ->assertSeeLink('編集')->assertSeeIn('main .tabs .active', '編集')
                ->assertSeeLink('コメント欄')
                
                ->assertInputValue('summary', '')
                ->type('summary', '太陽の画像を追加')
                ->assertInputValue('tags', "test\nテスト")
                ->assertSelected('category', 'generic')
                ->assertInputValue('locale', 'ja')
            ->click('#table tbody tr:nth-of-type(2) td:nth-of-type(1)');
                $remoteTargetLocator = $browser->driver->switchTo();
                $remoteTargetLocator->activeElement()->sendKeys(Keys::TAB)->sendKeys('sun.png');
                $browser->attach('added-files[]', $this->generateTempFile($this->generateImage(), 'sun.png'))
                ->assertNotChecked('deleted-file-names[]', 'charon.png')
            ->press('編集を反映');
                $this->assertSame($url, $browser->driver->getCurrentURL());
                $browser->assertSee('成功しました。')
            ->clickLink('お題一覧')
                ->assertVisible("main tbody tr:nth-of-type(1) td:nth-of-type(2) img[src='$url/files/sun.png']")
            ->logout();
        });
        return $url;
    }
    
    /**
     * 更新履歴。
     *
     * @depends testEdit
     * @param  string  $url
     * @return void
     */
    public function testRevisions(string $url): void
    {
        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit("$url/revisions")
                ->assertTitle('天体 — 更新履歴 | 辞書まとめwiki β版')
                ->assertSeeLink('ダウンロード')
                ->assertSeeLink('お題一覧 3')
                ->assertSeeLink('更新履歴')->assertSeeIn('main .tabs .active', '更新履歴')
                ->assertSeeLink('編集')
                ->assertSeeLink('コメント欄')
                
                ->assertSeeIn('main thead th:nth-of-type(1)', '更新日時')
                ->assertSeeIn('main thead th:nth-of-type(2)', '更新内容の要約')
                ->assertSeeIn('main thead th:nth-of-type(3)', 'ユーザー')
                
                ->assertChecked('main tbody tr:nth-of-type(1) [name="revisions[]"]')
                ->assertVisible("main tbody tr:nth-of-type(1) th a[href^='$url/revisions/']")
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(2)', '太陽の画像を追加')
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(3)', 'ギットハブ名')
                
                ->assertChecked('main tbody tr:nth-of-type(2) [name="revisions[]"]')
                ->assertVisible("main tbody tr:nth-of-type(2) th a[href^='$url/revisions/']")
                //->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(2)', '')
                ->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(3)', 'ギットハブ名')
                
            ->press('選択したバージョン同士を比較');
                $this->assertStringMatchesFormat(
                    "$url/revisions/diff?revisions%5B%5D=%d&revisions%5B%5D=%d",
                    $browser->driver->getCurrentURL()
                );
                $browser->assertTitle('「天体」の差分 | 辞書まとめwiki β版')
                ->assertSeeLink('天体')
                
                ->assertSeeIn('main thead th:nth-of-type(1)', '更新日時')
                ->assertSeeIn('main thead th:nth-of-type(2)', '更新内容の要約')
                ->assertSeeIn('main thead th:nth-of-type(3)', 'ユーザー')
                
                ->assertSeeIn('main tbody tr:nth-of-type(1) th', '旧')
                ->assertVisible("main tbody tr:nth-of-type(1) td:nth-of-type(1) a[href^='$url/revisions/']")
                //->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(2)', '')
                ->assertSeeIn('main tbody tr:nth-of-type(1) td:nth-of-type(3)', 'ギットハブ名')
                
                ->assertSeeIn('main tbody tr:nth-of-type(2) th', '新')
                ->assertVisible("main tbody tr:nth-of-type(2) td:nth-of-type(1) a[href^='$url/revisions/']")
                ->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(2)', '太陽の画像を追加')
                ->assertSeeIn('main tbody tr:nth-of-type(2) td:nth-of-type(3)', 'ギットハブ名')
                    
                ->assertSee('sun.png')
                
                ->assertSeeIn('.highlighter thead th:nth-of-type(1)', '@@')
                ->assertSeeIn('.highlighter thead th:nth-of-type(2)', 'text')
                ->assertSeeIn('.highlighter thead th:nth-of-type(3)', 'image')
                ->assertSeeIn('.highlighter thead th:nth-of-type(4)', 'answer')
                ->assertSeeIn('.highlighter thead th:nth-of-type(5)', '...')
                
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(1) td:nth-of-type(1)', '->')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(1) td:nth-of-type(2)', '太陽')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(1) td:nth-of-type(3)', 'NULL->sun.png')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(1) td:nth-of-type(4)', 'たいよう')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(1) td:nth-of-type(5)', '...')
                
                //->assertSeeIn('.highlighter tbody tr:nth-of-type(2) td:nth-of-type(1)', '')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(2) td:nth-of-type(2)', '地球')
                //->assertSeeIn('.highlighter tbody tr:nth-of-type(2) td:nth-of-type(3)', '')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(2) td:nth-of-type(4)', 'ちきゅう')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(2) td:nth-of-type(5)', '...')
                
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(3) td:nth-of-type(1)', '...')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(3) td:nth-of-type(2)', '...')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(3) td:nth-of-type(3)', '...')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(3) td:nth-of-type(4)', '...')
                ->assertSeeIn('.highlighter tbody tr:nth-of-type(3) td:nth-of-type(5)', '...');
        });
    }
}
