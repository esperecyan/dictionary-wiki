辞書まとめwiki
==============
wiki形式の辞書アップローダーです。
保存は[主に単語で答えるゲームにおける汎用的な辞書形式]で行い、次のゲーム用の辞書を出力します。

* [キャッチフィーリング]、[Drawing Catch] \(*.cfq)
* [きゃっちま] \(*.dat)
* [Inteligenceω] \(*.txt, *.zip)
* [ピクトセンス]

[主に単語で答えるゲームにおける汎用的な辞書形式]: https://github.com/esperecyan/dictionary/blob/master/dictionary.md
[キャッチフィーリング]: http://www.forest.impress.co.jp/library/software/catchfeeling/
[Drawing Catch]: http://drafly.nazo.cc/games/olds/DC
[きゃっちま]: http://vodka-catchm.seesaa.net/article/115922159.html
[ピクトセンス]: http://pictsense.com/
[Inteligenceω]: http://loxee.web.fc2.com/inteli.html

wikiの作成
----------
1. 本PHPプロジェクトを作成する一つ上のディレクトリに移動します。  
   例: `cd /var/www`
1. プロジェクトを作成します。  
   * 開発用: `composer create-project --keep-vcs esperecyan/dictionary-wiki`
   * 実運用: `composer create-project --no-dev --keep-vcs esperecyan/dictionary-wiki`
1. プロジェクトルートに移動します。
   `cd dictionary-wiki`
1. 「storage」以下と「bootstrap/cache」以下にApacheから書き込めるようにします。  
   `chmod --recursive g+w {storage,bootstrap/cache}`  
   `sudo chgrp --recursive apache {storage,bootstrap/cache}`
1. 「.env」ファイルに、データベース設定、辞書検索用に[Algolia]の Application ID と Admin API Key、
	OAuthログイン用のクライアントIDとクライアントシークレットを記述します。
   実運用環境であれば、キャッシュの作成に利用する `APP_URL` にwikiトップページのURLを末尾のスラッシュを抜いて記述します。
   また、`FEED_TAGGING_ENTITY` も記述しておきます。
1. キャッシュを生成し (実運用環境)、マイグレーションを実行します。  
   * 開発用: `composer run-script --dev post-install-cmd`
   * 実運用: `composer run-script --no-dev post-install-cmd`
1. 「.apache.conf」ファイルを、wikiを設置する `<VirstualHost>` セクション内で `Include` します。
1. Apacheを再起動します。  
   `sudo apachectl graceful`

Algolia の Searchable Attributes (Rankingタブ) では、以下を設定しておきます。

1. title
1. tags
1. summary
1. recordTexts

[Algolia]: https://www.algolia.com/ "Hosted Search API that delivers instant and relevant results from the first keystroke"

### 更新 (実運用環境)
`git fetch`  
`git checkout $(git describe remotes/origin/master --tags)`  
`composer install --no-dev`

要件
----
* Apache (2.4 以上) モジュール版 PHP 7.1 以上
* php-mbstring ([mbstring拡張モジュール])

[mbstring拡張モジュール]: https://secure.php.net/mbstring "mbstring はマルチバイト対応の文字列関数を提供し、PHP でマルチバイトエンコーディングを処理することを容易にします。"

### 依存するライブラリ由来の要件
* PHP 64bit — [nelexa/zip] — [esperecyan/dictionary-php]
* php-intl ([Intl拡張モジュール]) — [esperecyan/dictionary-php]
* php-pecl-zip ([Zip拡張モジュール]) — [esperecyan/dictionary-php]
* php-pecl-imagick ([imagick (PECL拡張モジュール)]) — [esperecyan/dictionary-php]

[nelexa/zip]: https://packagist.org/packages/nelexa/zip
[esperecyan/dictionary-php]: https://packagist.org/packages/esperecyan/dictionary-php
[Intl拡張モジュール]: https://secure.php.net/intl "国際化用拡張モジュール (Intl と略します) は ICU ライブラリのラッパーです。 PHP プログラマが、UCA 準拠の照合順序 (collation) や日付/時刻/数値/通貨のフォーマットを扱えるようにします。"
[Zip拡張モジュール]: https://secure.php.net/zip "この拡張モジュールにより、ZIP 圧縮されたアーカイブとその内部のファイルに対する透過的な読み書きが可能となります。"
[imagick (PECL拡張モジュール)]: https://secure.php.net/imagick "Imagick は、ImageMagick API を使用して画像の作成や修正を行う ネイティブ PHP 拡張モジュールです。"

Contribution
------------
Pull Request、または Issue よりお願いいたします。

ライセンス
----------
当スクリプトのライセンスは [Mozilla Public License Version 2.0] \(MPL-2.0) です。

[Mozilla Public License Version 2.0]: https://www.mozilla.org/MPL/2.0/
