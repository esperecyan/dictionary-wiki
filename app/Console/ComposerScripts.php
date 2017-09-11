<?php

namespace App\Console;

use Illuminate\Foundation\Application;
use Composer\Script\Event;

class ComposerScripts
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    protected static $laravel;
    
    protected static function initialize(Event $event): void
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir') . '/autoload.php';
        static::$laravel = new Application(getcwd());
    }
    
    /**
     * Handle the post-root-package-install Composer event.
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postRootPackageInstall(Event $event): void
    {
        static::initialize($event);
        static::createEnv($event->isDevMode());
    }
    
    /**
     * まだ存在していなければ、「.env」ファイルを作成します。
     *
     * @param bool $dev Composerがdevモードで実行されていれば真。
     * @return void
     */
    protected static function createEnv(bool $dev): void
    {
        $envPath = static::$laravel->environmentFilePath();
        if (!file_exists($envPath)) {
            $env = file_get_contents('.env.example');
            file_put_contents($envPath, $dev ? $env : str_replace(
                ['APP_ENV=local'     , 'APP_DEBUG=true' , 'APP_LOG_LEVEL=debug' ],
                ['APP_ENV=production', 'APP_DEBUG=false', 'APP_LOG_LEVEL=notice'],
                $env
            ));
        }
    }
    
    /**
     * @inheritDoc
     */
    public static function postInstall(Event $event): void
    {
        static::initialize($event);
        
        if (static::isSetDBPassword()) {
            if (!$event->isDevMode()) {
                static::cache();
            }
            static::migrate();
        }
    }
    
    /**
     * データベースのパスワードが設定済みであれば真を返します。
     *
     * @return bool
     */
    protected static function isSetDBPassword(): bool
    {
        return preg_match('/^DB_PASSWORD=(?!secret$).+$/um', file_get_contents(static::$laravel->environmentFilePath()))
            === 1;
    }
    
    /**
     * configとrouteのキャッシュを生成します。
     *
     * @return void
     */
    protected static function cache(): void
    {
        system('php artisan config:cache; php artisan route:cache');
    }
    
    /**
     * マイグレーションを実行します。
     *
     * @return void
     */
    protected static function migrate(): void
    {
        system('php artisan migrate --force');
    }
}
