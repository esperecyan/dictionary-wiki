<?php

namespace Tests;

trait GeneratesTempFile
{
    /**
     * スクリプト終了時に自動的に削除されるファイルを作成し、そのパスを返します。
     *
     * @param  string  $file 複製するファイルパス、または書き込む文字列。
     * @param  string|null  $filename ファイル名を固定する必要がある場合に指定。
     * @return string
     */
    protected function generateTempFile(string $file, string $filename = null): string
    {
        $path = tempnam(sys_get_temp_dir(), 'php');
        $directoryPath = isset($filename) ? $path : null;
        if ($directoryPath) {
            $path = "$directoryPath/$filename";
            unlink($directoryPath);
            mkdir($directoryPath);
        }
        
        file_put_contents(
            $path,
            strpos($file, "\x00") === false && file_exists($file) ? file_get_contents($file) : $file
        );
        register_shutdown_function(function (string $path, ?string $directoryPath) {
            if (file_exists($path)) {
                unlink($path);
            }
            if ($directoryPath && is_dir($directoryPath)) {
                rmdir($directoryPath);
            }
        }, $path, $directoryPath);
        return $path;
    }
}
