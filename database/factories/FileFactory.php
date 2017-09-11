<?php

use Faker\Generator as Faker;

$factory->define(App\File::class, function (Faker $faker) {
    $type = $faker->randomElement(array_keys(App\File::EXTENSIONS));
    return [
        'type' => $type,
        'name' => $faker->word . '.' . App\File::EXTENSIONS[$type],
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
    ];
});
