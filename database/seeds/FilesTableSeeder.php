<?php

use Illuminate\Database\Seeder;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class FilesTableSeeder extends Seeder
{
    /**
     * Fakerによって取得するランダムな画像データの数。
     *
     * @var int
     */
    const FAKE_FILES = 20;
    
    /**
     * 1つのテスト辞書データに結び付けるテストファイルデータの最大数。
     *
     * @var int
     */
    const MAX_FILES = 5;
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $files = $this->getFakeFiles();
        
        $faker = Faker\Factory::create();
        foreach (App\Dictionary::all() as $dictionary) {
            $fileCount = $faker->optional()->numberBetween(1, static::MAX_FILES);
            if ($fileCount) {
                $directory = storage_path('app/' . App\File::DIRECTORY_NAME . "/$dictionary->id");
                if (!file_exists($directory)) {
                    mkdir($directory);
                }
                
                $faker->unique(true);
                $revisionIds = $dictionary->revisions()->pluck('id')->toArray();
                $dictionary->files()->saveMany(
                    factory(App\File::class, $fileCount)->make()
                        ->each(function (App\File $file) use ($faker, $files, $directory, $revisionIds) {
                            $fakeFile = $faker->randomElement($files);
                            $file->name = "{$faker->unique()->word}." . App\File::EXTENSIONS[$fakeFile['type']];
                            $file->type = $fakeFile['type'];
                            $file->revision_id = $faker->randomElement($revisionIds);
                            file_put_contents("$directory/{$file->name}", $fakeFile['bytes']);
                        })
                );
                
                $dictionary->revision->files = $dictionary->files->pluck('revision_id', 'name');
                $dictionary->revision->save();
            }
        }
    }
    
    /**
     * ランダムなファイルを取得します。
     *
     * @return string[][] bytesキーの値に内容、typeキーの値にMIMEタイプを持つ配列の配列。
     */
    protected function getFakeFiles(): array
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < static::FAKE_FILES; $i++) {
            $files[] = [
                'bytes' => file_get_contents($faker->imageUrl(
                    $faker->numberBetween(1, App\File::MAX_RECOMMENDED_IMAGE_WIDTH),
                    $faker->numberBetween(1, App\File::MAX_RECOMMENDED_IMAGE_HEIGHT)
                )),
                'type' => $this->getContentType($http_response_header),
            ];
        }
        return $files;
    }
    
    /**
     * フィールド名とフィールド値がコロンで区切られた文字列配列から、content-typeヘッダフィールド値を取得します。
     *
     * @param string[] $headers
     *
     * @return string
     */
    protected function getContentType(array $headers): string
    {
        preg_match('/^\\s*content-type\\s*:\\s*(.+)\\s*$/im', implode("\r\n", $headers), $matches);
        return $matches[1];
    }
}
