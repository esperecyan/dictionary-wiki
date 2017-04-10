<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;

/**
 * トップページのテスト。
 */
class HomePageTest extends DuskTestCase
{
    /**
     * ログイン前。
     *
     * @return void
     */
    public function testWithoutLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage())
                ->assertSeeLink('ログイン')->assertSeeLink('ユーザー登録');
        });
    }
    
    /**
     * ログイン後。
     *
     * @return void
     */
    public function testWithLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)->visit(new HomePage())
                ->assertSeeLink('ギットハブ名')
                ->click('.dropdown-toggle')
                ->assertSeeLink('辞書の新規作成')->assertSeeLink('ユーザー設定')->assertSeeLink('プロフィール')
                ->assertSee('ログアウト');
        });
    }
}
