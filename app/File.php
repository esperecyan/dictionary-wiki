<?php

namespace App;

use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo};

class File extends Model
{
    /**
     * 拡張子を除くファイル名の最大長。
     *
     * @var int
     */
    const MAX_FILENAME_LENGTH = 25;
    
    /**
     * 拡張子を含むファイル名の最大長。
     *
     * @var int
     */
    const MAX_FILENAME_LENGTH_WITH_EXTENSION = 30;
    
    /**
     * アーカイブ中のファイル数の上限。
     *
     * @var int
     */
    const MAX_FILES = 10000;
    
    /**
     * 推奨される画像の最大の幅。ピクセル数。
     *
     * @var int
     */
    const MAX_RECOMMENDED_IMAGE_WIDTH = 1000;
    
    /**
     * 推奨される画像の最大の高さ。ピクセル数。
     *
     * @var int
     */
    const MAX_RECOMMENDED_IMAGE_HEIGHT = 1000;
    
    /**
     * 同梱可能なファイル形式に対応する拡張子 (先頭のドットを除く)。対応するMIMEタイプをキーに持ちます。
     *
     * @var string[]
     */
    const EXTENSIONS = [
        'image/png'     => 'png',
        'image/jpeg'    => 'jpg',
        'image/svg+xml' => 'svg',
        'audio/mp4'     => 'm4a',
        'audio/mpeg'    => 'mp3',
        'video/mp4'     => 'mp4',
    ];
    
    /**
     * ファイルを保存するディレクトリ名。
     *
     * @var int
     */
    const DIRECTORY_NAME = 'files';
    
    /**
     * このファイルが追加されたリビジョンを取得します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function revision(): BelongsTo
    {
        return $this->belongsTo(Revision::class);
    }
}
