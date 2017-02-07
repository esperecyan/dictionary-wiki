@if ($dictionary = request()->route('dictionary'))
    <li><a href="{{ route('dictionaries.show', ['dictionary' => $dictionary->id]) }}">{{ $dictionary->title }}</a></li>
    <li>
        <a href="{{ route('dictionaries.threads.index', ['dictionary' => $dictionary->id]) }}">{{ _('スレッド一覧') }}</a>
        <a href="{{ route('dictionaries.threads.index', ['dictionary' => $dictionary->id, 'type' => 'atom']) }}"
            target="_blank" class="btn btn-default fa fa-feed">
            <span class="text-hide">フィード</span>
        </a>
    </li>
@elseif ($user = request()->route('user'))
    <li><a href="{{ route('users.show', ['user' => $user->id]) }}">{{ $user->name }}</a></li>
    <li>
        <a href="{{ route('users.threads.index', ['user' => $user->id]) }}">{{ _('スレッド一覧') }}</a>
        <a href="{{ route('users.threads.index', ['user' => $user->id, 'type' => 'atom']) }}"
            target="_blank" class="btn btn-default fa fa-feed">
            <span class="text-hide">フィード</span>
        </a>
    </li>
@else
    <li>
        <a href="{{ route('site.threads.index') }}">{{ _('スレッド一覧') }}</a>
        <a href="{{ route('site.threads.index', ['type' => 'atom']) }}"
            target="_blank" class="btn btn-default fa fa-feed">
            <span class="text-hide">フィード</span>
        </a>
    </li>
@endif
