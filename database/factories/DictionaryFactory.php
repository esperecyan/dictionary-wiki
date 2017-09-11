<?php

use Faker\Generator as Faker;

$factory->define(App\Dictionary::class, function (Faker $faker) {
    $filePath = $faker->file(base_path('tests/dictionaries'), sys_get_temp_dir());
    $dictionary = (new esperecyan\dictionary_php\Parser())->parse(new SplFileInfo($filePath));
    File::delete($filePath);
    
    $metadata = $dictionary->getMetadata();
    
    return [
        'category' => $faker->randomElement(App\Dictionary::CATEGORIES),
        'locale' => $faker->randomElement([config('app.locale'), $faker->languageCode]),
        'title' => $metadata['@title'] ?? 'dictionary',
        'words' => count($dictionary->getWords()),
        'summary' => $metadata['@summary']['lml'] ?? null,
        'regard' => $metadata['@regard'] ?? null,
        'latest' => $dictionary,
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'deleted_at' => $faker->optional()->dateTime('now', config('timezone')),
    ];
});
