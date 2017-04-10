<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\ModifyDictionaryRequest;

class ModifyDictionaryRequestTest extends TestCase
{
    /**
     * @prama  (string|string[])[]  $rules
     * @param  string[]  $keys
     * @param  string[][]  $expected
     * @return void
     *
     * @dataProvider rulesProvider
     */
    public function testRequireRules(array $rules, array $keys, array $expected): void
    {
        $this->assertEquals($expected, (new ModifyDictionaryRequest())->requireRules($rules, $keys));
    }
    
    public function rulesProvider(): array
    {
        return [
            [
                [
                    'tags'       => ['string'],
                    'summary'    => ['string', 'max:500'],
                    'category'   => ['in:generic,specific,private'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400'],
                    'csv'        => ['string'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string'],
                ],
                ['category', 'locale'],
                [
                    'tags'       => ['string', 'nullable'],
                    'summary'    => ['string', 'max:500', 'nullable'],
                    'category'   => ['in:generic,specific,private', 'required'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u', 'required'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400', 'nullable'],
                    'csv'        => ['string', 'nullable'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string', 'nullable'],
                ],
            ],
            [
                [
                    'tags'       => ['string'],
                    'summary'    => ['string', 'max:500'],
                    'category'   => ['in:generic,specific,private'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400'],
                    'csv'        => ['string'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string'],
                ],
                ['category', 'locale', 'dictionary'],
                [
                    'tags'       => ['string', 'nullable'],
                    'summary'    => ['string', 'max:500', 'nullable'],
                    'category'   => ['in:generic,specific,private', 'required'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u', 'required'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file', 'required'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400', 'nullable'],
                    'csv'        => ['string', 'nullable'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string', 'nullable'],
                ],
            ],
            [
                [
                    'tags'       => ['string'],
                    'summary'    => ['string', 'max:500'],
                    'category'   => ['in:generic,specific,private'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400'],
                    'csv'        => ['string'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string'],
                ],
                ['category', 'locale', 'csv'],
                [
                    'tags'       => ['string', 'nullable'],
                    'summary'    => ['string', 'max:500', 'nullable'],
                    'category'   => ['in:generic,specific,private', 'required'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u', 'required'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400', 'nullable'],
                    'csv'        => ['string', 'required'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string', 'nullable'],
                ],
            ],
            [
                [
                    'tags'       => ['string'],
                    'summary'    => ['string', 'max:500'],
                    'category'   => ['in:generic,specific,private'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400'],
                    'csv'        => ['string'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string'],
                ],
                ['summary'],
                [
                    'tags'       => ['string', 'nullable'],
                    'summary'    => ['string', 'max:500', 'required'],
                    'category'   => ['in:generic,specific,private'],
                    'locale'     => ['string', 'max:35', 'regex:/^[0-9A-Za-z]{1,8}(-[0-9A-Za-z]{1,8})*$/u', 'nullable'],
                    'uploading'  => ['boolean'],
                    'dictionary' => ['file'],
                    'type'       => ['in:csv,cfq,dat,quiz,siri,pictsense'],
                    'name'       => ['string', 'max:400', 'nullable'],
                    'csv'        => ['string', 'nullable'],
                    'added-files'          => ['array'],
                    'added-files.*'        => ['dictionary_file'],
                    'deleted-file-names'   => ['array'],
                    'deleted-file-names.*' => ['string', 'nullable'],
                ],
            ],
        ];
    }
}
