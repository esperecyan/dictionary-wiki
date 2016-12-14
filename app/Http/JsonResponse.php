<?php

namespace App\Http;

use Illuminate\Http\JsonResponse as BaseJsonResponse;

/**
 * JSON形式にするオプションの既定値として、0 の代わりに JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE を持つ。
 */
class JsonResponse extends BaseJsonResponse
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $data = null,
        $status = self::HTTP_OK,
        $headers = [],
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    ) {
        parent::__construct($data, $status, $headers, $options);
    }
}
