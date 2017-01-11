<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use bantu\IniGetWrapper\IniGetWrapper;

if (! function_exists('show_time')) {
    /**
     * time要素を生成します。
     *
     * @param  \Carbon\Carbon  $dateTime
     *
     * @return \Illuminate\Support\HtmlString
     */
    function show_time(Carbon $dateTime): HtmlString
    {
        return new HtmlString('<time datetime="' . e($dateTime->toW3cString()) . '"
            title="' . e($dateTime->toDateTimeString() . $dateTime->format(' (P)')) . '">
            ' . e($dateTime->toDateString()) . '
        </time>');
    }
    
    /**
     * アイコン付きのユーザー名のリンクを生成します。
     *
     * @param  \App\User  $user
     *
     * @return \Illuminate\Support\HtmlString
     */
    function show_user(User $user): HtmlString
    {
        return link_to_route(
            'users.show',
            new HtmlString('<img src="' . e($user->avatar ?: asset('img/no-avatar.png')) . '" alt="" />'
                . '<bdi>' . e($user->name) . '</bdi>'),
            ['user' => $user->id],
            ['class' => 'user']
        );
    }
}

if (! function_exists('get_upload_max_filesize')) {
    /**
     * php.iniディレクティブのget_upload_max_filesizeの値を、バイト数で返します。
     *
     * @return int
     */
    function get_upload_max_filesize(): int
    {
        return (new IniGetWrapper())->getBytes('upload_max_filesize');
    }
}

if (! function_exists('errors')) {
    /**
     * セッションからエラーメッセージを取得します。
     *
     * @param  string|null  $name
     * @return string[]
     */
    function errors(string $name = null): array
    {
        return session()->has('errors')
            ? (is_null($name) ? session('errors')->all() : session('errors')->get($name))
            : [];
    }
}

/**
 * 指定したモデルに対応するパラメータ名を取得します。
 *
 * @param  \Illuminate\Database\Eloquent\Model|string  $model
 * @return string
 */
function parameter_name($model): string
{
    return snake_case(class_basename($model), '-');
}

/**
 * 指定したモデルに対応するリソース名を取得します。
 *
 * @param  \Illuminate\Database\Eloquent\Model|string  $model
 * @return string
 */
function resource_name($model): string
{
    return str_replace('_', '-', str_plural(snake_case(class_basename($model))));
}
