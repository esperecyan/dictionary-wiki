<?php

use Symfony\Component\HttpFoundation\File\MimeType\{FileinfoMimeTypeGuesser, MimeTypeGuesser};

MimeTypeGuesser::getInstance()->register(new class extends FileinfoMimeTypeGuesser {
    /**
     * @inheritDoc
     */
    public function guess($path): ?string
    {
        $type = parent::guess($path);
        return $type === 'video/mp4' && (new getID3())->analyze($path)['mime_type'] === 'audio/mp4'
            ? 'audio/mp4'
            : $type;
    }
});
