@if ($dictionary = request()->route('dictionary'))
    <li><a href="{{ route('dictionaries.show', ['dictionary' => $dictionary->id]) }}">{{ $dictionary->title }}</a></li>
    <li><a href="{{ route('dictionaries.threads.index', ['dictionary' => $dictionary->id]) }}">{{ _('スレッド一覧') }}</a></li>
@elseif ($user = request()->route('user'))
    <li><a href="{{ route('users.show', ['user' => $user->id]) }}">{{ $user->name }}</a></li>
    <li><a href="{{ route('users.threads.index', ['user' => $user->id]) }}">{{ _('スレッド一覧') }}</a></li>
@else
    <li><a href="{{ route('site.threads.index') }}">{{ _('スレッド一覧') }}</a></li>
@endif
