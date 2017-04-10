<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class RegistersTest extends TestCase
{
    /**
     * @param  string  $path
     * @param  string|null  $mimeType
     * @return void
     *
     * @dataProvider mimeTypeProvider
     */
    public function testGuess(string $path, ?string $mimeType): void
    {
        $this->assertSame($mimeType, (new File($path))->getMimeType());
    }
    
    public function mimeTypeProvider(): array
    {
        return [
            [__DIR__ . '/../../resources/mpeg4-aac.m4a'         , 'audio/mp4' ],
            [__DIR__ . '/../../resources/mpeg4-h264.mp4'        , 'video/mp4' ],
            [__DIR__ . '/../../resources/mpeg1-audio-layer3.mp3', 'audio/mpeg'],
            [__DIR__ . '/../../resources/ogg-vorbis.ogg'        , 'audio/ogg' ],
        ];
    }
}
