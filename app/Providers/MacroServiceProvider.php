<?php

namespace App\Providers;

use App\Dictionary;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\HtmlString;
use Html;
use Form;
use Markdown;
use esperecyan\html_filter\Filter;
use League\HTMLToMarkdown\HtmlConverter;
use Masterminds\HTML5;
use DOMElement;
use DOMText;

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
        
        Html::macro('convertField', function (string $field): HtmlString {
            return new HtmlString((new Filter(
                null,
                ['before' => function (DOMElement $body): void {
                    foreach (array_merge(...array_map(function (string $elementName) use ($body) {
                        return iterator_to_array($body->getElementsByTagName($elementName));
                    }, ['img', 'audio', 'video'])) as $embededContent) {
                        $src = $embededContent->getAttribute('src');
                        if (preg_match('/^[-.0-9_a-z]+$/', $src)) {
                            $embededContent->setAttribute('src', route(
                                'dictionaries.files.show',
                                ['dictionary' => request()->route('dictionary'), 'file' => $src]
                            ));
                        } else {
                            $embededContent->parentNode->replaceChild(
                                new DOMText((new HtmlConverter())->convert((new HTML5())->saveHTML($embededContent))),
                                $embededContent
                            );
                        }
                    }
                }]
            ))->filter(Markdown::convertToHtml($field)));
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
