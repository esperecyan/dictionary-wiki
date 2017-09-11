<?php

use Faker\Generator as Faker;

$factory->define(App\Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->word,
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
    ];
});
