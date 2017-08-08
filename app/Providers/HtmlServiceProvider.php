<?php

namespace App\Providers;

use Illuminate\Support\HtmlString;
use Collective\Html\{HtmlServiceProvider as BaseHtmlServiceProvider, HtmlBuilder, FormBuilder};

class HtmlServiceProvider extends BaseHtmlServiceProvider
{
    /**
     * @inheritDoc
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
            return new class ($app['url'], $app['view']) extends HtmlBuilder {
                /**
                 * {@inheritDoc}
                 *
                 * @param string|\Illuminate\Support\HtmlString $value
                 *
                 * @return string|\Illuminate\Support\HtmlString $value が HtmlString の場合、変換せずそのまま返します。
                 */
                public function entities($value)
                {
                    return $value instanceof HtmlString ? $value : parent::entities($value);
                }
            };
        });
    }

    /**
     * @inheritDoc
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new class (
                $app['html'],
                $app['url'],
                $app['view'],
                $app['session.store']->token()
            ) extends FormBuilder {
                /**
                 * @inheritDoc
                 */
                public function open(array $options = [])
                {
                    $html = (string)parent::open($options);
                    if (isset($options['files']) && $options['files']) {
                        $html .= $this->hidden('MAX_FILE_SIZE', get_upload_max_filesize());
                    }
                    return $this->toHtmlString($html);
                }
                
                /**
                 * @inheritDoc
                 */
                public function input($type, $name, $value = null, $options = [])
                {
                    return $this->formControl(parent::input($type, $name, $value, $options), $name, $options, $type);
                }
                
                /**
                 * @inheritDoc
                 */
                public function textarea($name, $value = null, $options = [])
                {
                    return $this->formControl(parent::textarea($name, $value, $options), $name, $options);
                }
                
                /**
                 * @inheritDoc
                 */
                public function select(
                    $name,
                    $list = [],
                    $selected = null,
                    array $selectAttributes = [],
                    array $optionsAttributes = []
                ) {
                    return $this->formControl(
                        parent::select($name, $list, $selected, $selectAttributes, $optionsAttributes),
                        $name,
                        $selectAttributes
                    );
                }
                
                /**
                 * フォームコントロールを返します。
                 *
                 * @param \Illuminate\Support\HtmlString $formControl
                 * @param string|null $name
                 * @param array $options
                 * @param string|null $type
                 * @return \Illuminate\Support\HtmlString
                 */
                protected function formControl(
                    HtmlString $formControl,
                    string $name = null,
                    array $options = [],
                    string $type = null
                ): HtmlString {
                    $html = (string)$formControl;
                    if ($name && !in_array($type, ['hidden', 'radio', 'submit', 'reset', 'button'])) {
                        if ($type !== 'checkbox') {
                            $html .= $this->errors($name);
                        }
                        if (isset($options['required'])) {
                            $html .= '<span class="label label-primary required">' . e(_('必須')) . '</span>';
                        }
                    }
                    return $this->toHtmlString($html);
                }

                /**
                 * @inheritDoc
                 */
                protected function placeholderOption($display, $selected)
                {
                    return $this->toHtmlString(preg_replace(
                        '/^<option/',
                        '$0' . $this->html->attributes(['disabled']),
                        parent::placeholderOption($display, $selected)
                    ));
                }
            };

            return $form->setSessionStore($app['session.store']);
        });
    }
}
