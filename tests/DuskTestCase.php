<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Laravel\Dusk\Browser;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, GeneratesTempFile;
    
    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()
        );
    }
    
    /**
     * クリップボード内のテキストが指定値であることを確認します。
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $expected
     * @return void
     */
    protected function assertClipboardText(Browser $browser, string $expected): void
    {
        $browser->script('document.body.insertAdjacentHTML("beforeend", "<textarea></textarea>");');
        $browser->keys('> textarea:last-of-type', ['{control}', 'v'])
            ->assertValue('> textarea:last-of-type', $expected);
        $browser->script('document.querySelector("body > textarea:last-of-type").remove();');
    }
    
    /**
     * クリップボード内のテキストを空にします。
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    protected function clearClipboardText(Browser $browser): void
    {
        $browser->script('
            document.body.insertAdjacentHTML("beforeend", "<input />");
            document.querySelector("body > input:last-of-type").addEventListener("copy", function clear(event) {
                event.preventDefault();
                event.clipboardData.setData("text", "");
            });
        ');
        $browser->keys('> input:last-of-type', ['{control}', 'c']);
        $browser->script('document.querySelector("body > input:last-of-type").remove();');
    }
}
