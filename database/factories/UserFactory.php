<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'profile' => $faker->optional()->text,
        'updated_at' => $faker->dateTime('now', config('timezone')),
        'created_at' => $faker->dateTime('now', config('timezone')),
        'deleted_at' => $faker->optional()->dateTime('now', config('timezone')),
    ];
});
