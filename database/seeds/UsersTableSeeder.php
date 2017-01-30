<?php

use Illuminate\Database\Seeder;

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
            
            $user->externalAccounts()->saveMany(
                factory(App\ExternalAccount::class, count($services))->make()->each(
                    function (App\ExternalAccount $externalAccount) use ($user, &$services) {
                        $externalAccount->user_id = $user->id;
                        $externalAccount->provider = array_shift($services);
                    }
                )
            );
                
            $externalAccounts = $user->externalAccounts()->get();
            
            $user->name_provider_id = $faker->randomElement($externalAccounts->toArray())['id'];
            $emaildProvider = $faker->optional()->randomElement($externalAccounts->where('email', true)->toArray());
            $user->email_provider_id = $emaildProvider['id'];
            $avatarProvider = $faker->optional()->randomElement($externalAccounts->where('avatar', true)->toArray());
            $user->avatar_provider_id = $avatarProvider['id'];
            $user->save();
        });
    }
}
