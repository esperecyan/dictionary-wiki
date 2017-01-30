<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'profile' => $faker->optional()->text,
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
        'deleted_at' => $faker->optional()->dateTime('now', config('timezone')),
    ];
});

$factory->define(App\ExternalAccount::class, function (Faker\Generator $faker) {
    return [
        'provider' => $faker->randomElement(config('auth.services')),
        'provider_user_id' => (string)$faker->uuid,
        'name' => $faker->name,
        'email' => $faker->optional()->safeEmail,
        'avatar' => $faker->optional()->imageUrl,
        'link' => $faker->optional()->url,
        'public' => $faker->boolean,
        'available' => $faker->boolean,
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
    ];
});

$factory->define(App\Dictionary::class, function (Faker\Generator $faker) {
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

$dictionaries = File::files(base_path('tests/dictionaries'));
$factory->define(App\Revision::class, function (Faker\Generator $faker) use ($dictionaries) {
    return [
        'data' => File::get($faker->randomElement($dictionaries)),
        'tags' => $faker->words($faker->numberBetween(0, App\Tag::MAX_TAGS)),
        'files' => array_map(function () use ($faker)/*: int*/ {
            return $faker->numberBetween(1, DictionariesTableSeeder::DATA_LENGTH);
        }, array_fill_keys(array_map(function (string $filename) use ($faker)/*: string */ {
            return "$filename." . $faker->randomElement(App\File::EXTENSIONS);
        }, $faker->words($faker->numberBetween(0, App\File::MAX_FILENAME_LENGTH))), null)),
        'summary' => $faker->text,
        'ipaddr' => $faker->{$faker->randomElement(['ipv4', 'ipv6'])},
        'external_accounts' => array_map(function () use ($faker)/*: array*/ {
            return (string)$faker->unique()->randomNumber;
        }, array_flip($faker->randomElements(config('auth.services')))),
        'created_at' => $faker->dateTime('now', config('timezone')),
    ];
});

$factory->define(App\File::class, function (Faker\Generator $faker) {
    $type = $faker->randomElement(array_keys(App\File::EXTENSIONS));
    return [
        'type' => $type,
        'name' => $faker->word . '.' . App\File::EXTENSIONS[$type],
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
    ];
});

$factory->define(App\Tag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
    ];
});
