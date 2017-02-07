<?php
use App\Http\Controllers\Forum\CategoriesController;
use Carbon\Carbon;

$id = 'tag:' . config('feed.taggingEntity') . ':dictionary-wiki:';
switch (request()->route()->getName()) {
    case 'dictionaries.revisions.index':
        $id .= 'dictionary-' . request()->route('dictionary')->id . ':revisions';
        break;
    case 'dictionaries.threads.index':
        $id .= 'dictionary-' . request()->route('dictionary')->id . ':threads';
        break;
    case 'users.threads.index':
        $id .= 'user-' . request()->route('user')->id . ':threads';
        break;
    case CategoriesController::SITE_FORUM_CATEGORY_TITLE . '.threads.index':
        $id .= CategoriesController::SITE_FORUM_CATEGORY_TITLE . ':threads';
        break;
}
?>
{!! '<'.'?'.'xml version="1.0" encoding="UTF-8" ?>' !!}
<feed xmlns="http://www.w3.org/2005/Atom"<?php foreach($namespaces as $n) echo " ".$n; ?>>
    <title type="text">{!! $channel['title'] !!}</title>
@if (isset($channel['description']))
    <subtitle type="xhtml">{{ $channel['description'] }}</subtitle>
@endif
    <link href="{{ Request::url() }}"></link>
    <id>{{ $id }}</id>
    <link rel="self" type="application/atom+xml" href="{{ $channel['link'] }}" ></link>
@if (!empty($channel['logo']))
    <logo>{{ $channel['logo'] }}</logo>
@endif
@if (!empty($channel['icon']))
    <icon>{{ $channel['icon'] }}</icon>
@endif
    <generator uri="https://github.com/RoumenDamianoff/laravel-feed">roumen/feed</generator>
        <updated>{{ $channel['pubdate'] }}</updated>
@foreach($items as $item)
        <entry>
            <author>
                <name>{{ $item['author'] }}</name>
                <uri>{{ $item['authorURL'] }}</uri>
            </author>
            <title type="text">{!! $item['title'] !!}</title>
            <link rel="alternate" href="{{ $item['link'] }}"></link>
            <id>{{ $item['id'] }}</id>
@if (isset($item['description']))
            <summary type="html"><![CDATA[{!! $item['description'] !!}]]></summary>
@endif
@if (isset($item['content']))
            <content>{{ $item['content'] }}</content>
@endif
            <updated>{{ $item['pubdate'] }}</updated>
        </entry>
@endforeach
</feed>
