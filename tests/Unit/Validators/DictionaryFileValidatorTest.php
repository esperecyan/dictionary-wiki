<?php

namespace Tests\Unit\Validators;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Validator;

class DictionaryFileValidatorTest extends TestCase
{
    /**
     * @param  string  $file
     * @param  string  $originalName
     * @param  string[]  $errors
     * @return void
     *
     * @dataProvider fileProvider
     */
    public function testValidate(string $file, string $originalName, array $errors): void
    {
        $validator = Validator::make(
            ['file' => new UploadedFile($this->generateTempFile($file), $originalName)],
            ['file' => 'dictionary_file'],
            ['dictionary_file' => '「error」と「critical」を修正する必要があります。']
        );
        if ($errors) {
            $this->assertEquals(
                array_merge($errors, ['「error」と「critical」を修正する必要があります。']),
                $validator->errors()->get('file')
            );
        } else {
            $this->assertTrue($validator->passes(), print_r($validator->errors()->get('file'), true));
        }
    }
    
    public function fileProvider(): array
    {
        return [
            [__DIR__ . '/../../resources/exif.jpg', 'test.jpg', []],
            [__DIR__ . '/../../resources/exif.jpg', 'test.jpeg', []],
            ['<?xml version="1.0" ?>
            <svg xmlns="http://www.w3.org/2000/svg">
                <rect width="1000" height="1000" />
            </svg>', 'test.svg', []],
            ['<?xml version="1.0" ?>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <a xlink:href="https://example.com/">
                    <rect width="1000" height="1000" />
                </a>
            </svg>', 'test.svg', []],
            [__DIR__ . '/../../resources/mpeg1-audio-layer3.mp3', 'test.mp3', []],
            [__DIR__ . '/../../resources/mpeg4-aac.m4a', 'test.m4a', []],
            [__DIR__ . '/../../resources/mpeg4-aac.m4a', 'test.mp4', []],
            ['<?xml version="1.0" ?>
            <svg>
                ルート要素がSVG名前空間に属していない。
            </svg>', 'test.svg', [
                'critical: 「test.svg」は妥当なSVGファイルではありません。',
            ]],
            [__DIR__ . '/../../resources/dummy.zip', 'test.zip', [
                'critical: 「test.zip」は妥当なファイル名ではありません。',
            ]],
            [__DIR__ . '/../../resources/exif.jpg', 'test.jpe', [
                'critical: 「test.jpe」は妥当なファイル名ではありません。',
            ]],
            [__DIR__ . '/../../resources/exif.jpg', 'test.png', [
                'critical: 「test.png」の拡張子は次のいずれかにしなければなりません: jpg, jpeg',
            ]],
            [__DIR__ . '/../../resources/exif.jpg', '-test.jpg', [
                'critical: 「-test.jpg」は妥当なファイル名ではありません。',
            ]],
        ];
    }
}
