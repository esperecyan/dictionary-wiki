<?php

use Faker\Generator as Faker;

$factory->define(App\ExternalAccount::class, function (Faker $faker) {
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
