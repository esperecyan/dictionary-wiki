<?php

namespace App\Providers;

use App\Dictionary;
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
        
        Html::macro('showDictionaryWarnings', function (Dictionary $dictionary): HtmlString {
            $html = '';
            
            if ($dictionary->regard) {
                $html .= '<span class="message-icon" title="' . e(_('ひらがな (カタカナ) 以外が答えに含まれるお題があります')) . '">
                    <i class="fa fa-language"></i>
                    <span class="text-hide">' . e(_('ひらがな (カタカナ) 以外が答えに含まれるお題があります')) . '</span>
                </span>';
            }
            
            return new HtmlString(
                ($html ? '<i class="fa fa-exclamation-triangle"></i>' : '') . $html
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
