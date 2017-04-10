<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * 固定データを除くテストユーザーデータの作成数。
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
        $this->createUserHavingFixedData();
        
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
    
    public function createUserHavingFixedData(): void
    {
        $user = factory(App\User::class)->create(['id' => 1]);
        $user->externalAccounts()->saveMany([
            factory(App\ExternalAccount::class)->make([
                'user_id' => 1,
                'provider' => 'github',
                'provider_user_id' => '0',
                'name' => 'ギットハブ名',
                'email' => 'github@dictionary-wiki.test',
                'avatar' => 'https://avatars.githubusercontent.com/u/9919',
                'link' => 'https://github.com/github',
                'public' => false,
                'available' => true,
            ]),
            /*factory(App\ExternalAccount::class)->make([
                'user_id' => 1,
                'provider' => 'facebook',
                'provider_user_id' => '0',
                'name' => 'フェイスブック名',
                'email' => null,
                'avatar' => 'https://graph.facebook.com/v2.8/facebook/picture?type=normal',
                'link' => 'https://www.facebook.com/FacebookforDevelopers/',
                'public' => true,
                'available' => true,
            ]),*/
            factory(App\ExternalAccount::class)->make([
                'provider' => 'google',
                'provider_user_id' => '0',
                'name' => 'グーグル名',
                'email' => 'google@dictionary-wiki.test',
                'avatar' => 'https://lh3.googleusercontent.com/-v0soe-ievYE/AAAAAAAAAAI/AAAAAAADuHY/LZlLy-ckw8I/photo.jpg?sz=50',
                'link' => 'https://plus.google.com/116899029375914044550',
                'public' => false,
                'available' => false,
            ]),
            factory(App\ExternalAccount::class)->make([
                'provider' => 'linkedin',
                'provider_user_id' => 'AzAzAzAzAz',
                'name' => 'リンクトイン名',
                'email' => 'linkedin@dictionary-wiki.test',
                'avatar' => null,
                'link' => null,
                'public' => false,
                'available' => true,
            ]),
            factory(App\ExternalAccount::class)->make([
                'provider' => 'twitter',
                'provider_user_id' => '0',
                'name' => 'ツイッター名',
                'email' => null,
                'avatar' => 'https://pbs.twimg.com/profile_images/842992378960986112/Yd1Z53jW_normal.jpg',
                'link' => 'https://twitter.com/twitter',
                'public' => false,
                'available' => true,
            ]),
        ]);
        
        $externalAccountIds = $user->externalAccounts()->get()->pluck('id', 'provider');
        $user->name_provider_id = $externalAccountIds['github'];
        $user->email_provider_id = $externalAccountIds['linkedin'];
        $user->avatar_provider_id = $externalAccountIds['twitter'];
        $user->deleted_at = null;
        $user->save();
    }
}
