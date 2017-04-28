@extends('layouts.app')

@push('styles')
    <link href="{{ asset('css/sticky-footer-navbar.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">{{ _('辞書まとめwiki') }}</div>

    <div class="panel-body">
        <p>
            {{ _('次のゲームで使う辞書のアップローダーです。') }}
        </p>
        <ul>
            <li><a href="http://www.forest.impress.co.jp/library/software/catchfeeling/" rel="external" target="_blank">
                {{ _('キャッチフィーリング') }}
            </a></li>
            <li><a href="http://www.houmotsuko.net/game/old/drawcatch/index" rel="external" target="_blank">
                {{ _('Drawing Catch') }}
            </a></li>
            <li><a href="http://vodka-catchm.seesaa.net/article/115922159.html" rel="external" target="_blank">
                {{ _('きゃっちま') }}
            </a></li>
            <li><a href="http://loxee.web.fc2.com/inteli.html" rel="external" target="_blank">
                {{ _('Inteligenceω') }}
            </a></li>
            <li><a href="http://pictsense.com/" rel="external" target="_blank">
                {{ _('ピクトセンス') }}
            </a></li>
        </ul>
        <p>
            {{ _('アップロードした辞書は、あとから皆で更新していくことができます。このアップローダーには辞書の相互変換機能があるので、たとえばピクトセンスの辞書を登録したら、キャッチフィーリングやInteligenceωのしりとり辞書としても利用できます。') }}
        </p>
        <p>
            {{ _('アップローダーの各コンテンツへは、ページ上部のメニューからどうぞ。') }}
            <strong>{{ _('ウィンドウの幅が狭いときは折りたたまれます。') }}</strong>
        </p>
    </div>
</div>
@endsection

@section('footer')
<footer class="footer navbar navbar-default">
    <nav>
        <div class="container">
            <div class="collapse navbar-collapse" id="site-feedback">
                <ul class="nav navbar-nav navbar-right">
                    <li>{{ link_to_route('site.threads.index', _('サイトのフィードバック')) }}</li>
                    <li><a href="https://github.com/esperecyan/dictionary-wiki/blob/v0.1.3/resources/schemas/apis.md"
                        target="_blank" rel="external noopener noreferrer">
                        {{ _('開発者向けAPI') }}
                    </a></li>
                    <li><a href="https://github.com/esperecyan/dictionary-wiki/"
                        target="_blank" rel="external noopener noreferrer">
                        {{ _('GitHub') }}
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>
</footer>
@endsection
