<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\HtmlString;
use Html;
use Form;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::macro('errors', function (string $name): HtmlString {
            return new HtmlString(
                '<div class="help-block with-errors"><ul>' . implode('', array_map(function (string $error): string {
                    return '<li>' . e($error) . '</li>';
                }, errors($name))) . '</ul></div>'
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
