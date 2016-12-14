<?php

use Illuminate\Database\{Seeder, Eloquent\Collection};

class UsersTableSeeder extends Seeder
{
    /**
     * テストユーザーデータの作成数。
     *
     * @var int
     */
    const DATA_LENGTH = 50;
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, static::DATA_LENGTH)->create()->each(function (App\User $user) {
            $faker = Faker\Factory::create();
            $services = $faker->randomElements(
                config('auth.services'),
                $faker->numberBetween(1, count(config('auth.services')))
            );
            
            $collection = factory(App\ExternalAccount::class, count($services))->make();
            
            $user->externalAccounts()->saveMany(
                ($collection instanceof Collection ? $collection : collect([$collection]))->each(
                    function (App\ExternalAccount $externalAccount) use ($user, &$services) {
                        $externalAccount->user_id = $user->id;
                        $externalAccount->provider = array_shift($services);
                    }
                )
            );
                
            $externalAccounts = $user->externalAccounts()->get();
            
            $user->name_provider_id = $faker->randomElement($externalAccounts->toArray())['id'];
            $emaildProvider
                = $faker->optional()->randomElement($externalAccounts->whereLoose('email', true)->toArray());
            $user->email_provider_id = $emaildProvider['id'];
            $avatarProvider
                = $faker->optional()->randomElement($externalAccounts->whereLoose('avatar', true)->toArray());
            $user->avatar_provider_id = $avatarProvider['id'];
            $user->save();
        });
    }
}
