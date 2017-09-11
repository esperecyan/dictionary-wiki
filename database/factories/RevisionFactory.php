<?php

use Faker\Generator as Faker;

$dictionaries = File::files(base_path('tests/dictionaries'));
$factory->define(App\Revision::class, function (Faker $faker) use ($dictionaries) {
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
