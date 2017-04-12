<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class HomePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertTitle('辞書まとめwiki β版')
            ->assertSeeLink('辞書まとめwiki')->assertSeeLink('辞書一覧')->assertSeeLink('ユーザー一覧')
            ->assertSeeLink('サイトのフィードバック')->assertSeeLink('開発者向けAPI')->assertSeeLink('GitHub');
    }
}
