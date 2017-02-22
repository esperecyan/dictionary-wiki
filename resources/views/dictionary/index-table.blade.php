<table class="panel-body table">
    @if (count($dictionaries) > 0)
        <thead>
            <tr>
                <th></th>
                <th>@sortablelink('title', _('辞書名'))</th>
                <th class="text-right">@sortablelink('words', _('語数'))</th>
                <th>@sortablelink('updated_at', _('更新日時'))</th>
                <th></th>
            </tr>
        </thead>
    @endif
    <tbody>
        @if (count($dictionaries) > 0)
            @foreach($dictionaries as $dictionary)
                <tr>
                    <td>@if ($dictionary->category === 'specific')
                        <span class="message-icon" title="{{ $dictionary->categoryName }}">
                            <i class="fa fa-gamepad"></i>
                            <span class="text-hide">{{ $dictionary->categoryName }}</span>
                        </span>
                    @endif</td>
                    <th><bdi>{{
                        link_to_route('dictionaries.show', $dictionary->title, ['dictionary' => $dictionary->id])
                    }}</bdi></th>
                    <td class="text-right">{{ $dictionary->words }}</td>
                    <td>{!! show_time($dictionary->updated_at) !!}</td>
                    <td>{{ Html::showDictionaryWarnings($dictionary) }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center text-muted">
                    {{ $slot }}
                </td>
            </tr>
        @endif
    </tbody>
    @if (Route::currentRouteName() === 'dictionaries.index' && request()->has('search'))
    <tfoot>
        <tr>
            <td colspan="5" class="text-right">
                powered by
                <a href="https://www.algolia.com/" target="_blank" rel="external noopener noreferrer">
                    <img src="{{ asset('img/Algolia_logo_bg-white.svg') }}" alt="Algolia" height="17" />
                </a>
            </td>
        </tr>
    </tfoot>
    @endif
</table>
