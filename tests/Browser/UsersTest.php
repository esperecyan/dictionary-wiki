<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class UsersTest extends DuskTestCase
{
    /**
     * @return void
     */
    public function testIndex(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/users')
                ->assertSeeLink('ユーザー名')
                ->assertSee('外部アカウント')->assertDontSeeLink('外部アカウント')
                ->assertSeeLink('編集数')->assertVisible('thead th:nth-of-type(3) .fa-sort-amount-desc')
                ->assertSeeLink('最終辞書更新日時');
        });
    }
    
    /**
     * ユーザー詳細。
     *
     * @return void
     */
    public function testShow(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(1)->visit('/home/edit')
                ->radio('name-provider', 'github')
                ->assertSeeLink('GitHub')->check('public[]', 'github')
                ->assertSee('Facebook')->assertDontSeeLink('Facebook')
                    ->assertDontSeeIn('[action$="/home/edit"] tbody tr:nth-of-type(2) td:nth-of-type(2)', '有効')
                    ->assertDontSeeIn('[action$="/home/edit"] tbody tr:nth-of-type(2) td:nth-of-type(2)', '無効')
                ->assertSeeLink('Google')
                    ->assertSeeIn('[action$="/home/edit"] tbody tr:nth-of-type(3) td:nth-of-type(2)', '無効')
                ->assertSee('LinkedIn')->assertDontSeeLink('LinkedIn')
                ->assertSeeLink('Twitter')->check('public[]', 'twitter')
                ->clear('profile')
            ->click('[action$="/home/edit"] [type="submit"]');
                $this->assertSame(config('app.url') . '/home/edit', $browser->driver->getCurrentURL());
                $browser->assertRadioSelected('name-provider', 'github')
                ->assertChecked('public[]', 'github')
                ->assertChecked('public[]', 'twitter')
                ->assertInputValue('profile', '')
            ->visit('/users/1')
                ->assertTitle('ギットハブ名 | ユーザー | 辞書まとめwiki α版')
                ->assertSeeLink('詳細')->assertSeeIn('main .tabs .active', '詳細')
                ->assertSeeLink('個人用辞書')
                ->assertSeeLink('コメント欄')
                ->assertSeeLink('GitHub')
                ->assertDontSeeLink('Facebook')
                ->assertDontSeeLink('Google')
                ->assertDontSeeLink('LinkedIn')
                ->assertSeeLink('Twitter')
                ->assertDontSeeLink('自己紹介内のリンク')
            ->visit('/home/edit')
                ->radio('name-provider', 'twitter')
                ->uncheck('public[]', 'github')
                ->uncheck('public[]', 'twitter')
                ->type('profile', '[自己紹介内のリンク](https://dictionary-wiki.test/)')
            ->click('[action$="/home/edit"] [type="submit"]')
                ->assertRadioSelected('name-provider', 'twitter')
                ->assertNotChecked('public[]', 'github')
                ->assertNotChecked('public[]', 'twitter')
                ->assertInputValue('profile', '[自己紹介内のリンク](https://dictionary-wiki.test/)')
            ->visit('/users/1')
                ->assertTitle('ツイッター名 | ユーザー | 辞書まとめwiki α版')
                ->assertDontSeeLink('GitHub')
                ->assertDontSeeLink('Facebook')
                ->assertDontSeeLink('Google')
                ->assertDontSeeLink('LinkedIn')
                ->assertDontSeeLink('Twitter')
                ->assertSeeLink('自己紹介内のリンク')
            ->visit('/home/edit')->radio('name-provider', 'github')->click('[action$="/home/edit"] [type="submit"]');
        });
    }
    
    /**
     * 個人用辞書。
     *
     * @return void
     */
    public function testDictionaries(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/users/1/dictionaries')
                ->assertTitle('ギットハブ名 — 個人用辞書 | ユーザー | 辞書まとめwiki α版')
                ->assertSeeLink('詳細')
                ->assertSeeLink('個人用辞書')->assertSeeIn('main .tabs .active', '個人用辞書')
                ->assertSeeLink('コメント欄');
        });
    }
    
    /**
     * コメント欄。
     *
     * @return void
     */
    public function testThreads(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(1)->visit('/users/1/threads')
                ->assertTitle('1 - Forum | 辞書まとめwiki α版')
                ->assertSeeLink('スレッド一覧')
                ->assertSee('New thread')
            ->clickLink('New thread')
                ->type('title', 'スレッド作成テスト　タイトル')
                ->type('content', 'スレッド作成テスト　本文')
            ->press('Create')
                ->assertTitleContains('スレッド作成テスト　タイトル - 1 - Forum | 辞書まとめwiki α版')
                ->assertSeeLink('スレッド作成テスト　タイトル')
                ->assertSee('スレッド作成テスト　本文');
        });
    }
    
    /**
     * ログイン前のコメント欄。
     *
     * @return void
     */
    public function testThreadsWithoutLogin(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->logout()->visit('/users/1/threads')
                ->assertDontSee('New thread');
        });
    }
    
    /**
     * ログイン前のユーザー情報編集画面。
     *
     * @return void
     */
    public function testEditWithoutLogin(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->assertSame(
                config('app.url') . '/login',
                $browser->logout()->visit('/home/edit')->driver->getCurrentURL()
            );
        });
    }
    
    /**
     * ログイン画面。
     *
     * @return void
     */
    public function testLogin(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->logout()->visit('/login')
                ->assertTitle('ログイン | 辞書まとめwiki α版')
            ->press('GitHub');
                $this->assertStringStartsWith('https://github.com/login?', $browser->driver->getCurrentURL());
                $browser->assertTitle('Sign in to GitHub · GitHub')
                ->assertSee('辞書まとめwiki')
            ->back()->press('Facebook');
                $this->assertStringStartsWith('https://www.facebook.com/login.php?', $browser->driver->getCurrentURL());
                $browser->assertTitle('Facebookにログイン | Facebook')
            ->back()->press('Google');
                $this->assertStringStartsWith(
                    'https://accounts.google.com/o/oauth2/auth?',
                    $browser->driver->getCurrentURL()
                );
                $browser->assertTitle('Error 400 (OAuth2 Error)!!1')
                ->assertSee('Invalid parameter value for redirect_uri: Non-public domains not allowed:')
            ->back()->press('LinkedIn');
                $this->assertStringStartsWith('https://www.linkedin.com/uas/login?', $browser->driver->getCurrentURL());
                $this->assertRegExp('/^LinkedIn(?: にサインイン|にログイン)$/', $browser->driver->getTitle());
            $browser->back()->press('Twitter');
                $this->assertStringStartsWith(
                    'https://api.twitter.com/oauth/authenticate?',
                    $browser->driver->getCurrentURL()
                );
                $browser->assertTitle('Twitter / アプリケーション認証')
                ->assertSee('辞書まとめwiki');
        });
    }
}
