<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use App\Http\Middleware\VerifyUploadFileWithoutError;
use Illuminate\Http\{Request, Response, UploadedFile};
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class VerifyUploadFileWithoutErrorTest extends TestCase
{
    /**
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile[]  $files
     * @param  bool  $exception
     * @return void
     *
     * @dataProvider filesProvider
     */
    public function testHandle(array $files, bool $exception): void
    {
        if ($exception) {
            $this->expectException(UploadException::class);
        }
        (new VerifyUploadFileWithoutError())->handle(new Request([], [], [], [], $files), function () {
            return new Response();
        });
    }
    
    public function filesProvider(): array
    {
        return [
            [[
                new UploadedFile(__DIR__ . '/../../../resources/mpeg1-audio-layer3.mp3', '', null, null, null, true),
                new UploadedFile(__DIR__ . '/../../../resources/mpeg4-aac.m4a', '', null, null, null, true),
                new UploadedFile(__DIR__ . '/../../../resources/mpeg4-h264.mp4', '', null, null, null, true),
            ], false],
            [[
                new UploadedFile(__DIR__ . '/../../../resources/mpeg1-audio-layer3.mp3', '', null, null, null, true),
                new UploadedFile('', '', null, null, UPLOAD_ERR_INI_SIZE, true),
                new UploadedFile(__DIR__ . '/../../../resources/mpeg4-h264.mp4', '', null, null, null, true),
            ], true],
        ];
    }
}
