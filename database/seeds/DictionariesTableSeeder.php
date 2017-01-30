<?php

use Illuminate\Database\Seeder;

class DictionariesTableSeeder extends Seeder
{
    /**
     * テスト辞書データの作成数。
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
        factory(App\Dictionary::class, static::DATA_LENGTH)->create(['deleted_at' => null])
            ->each(function (App\Dictionary $dictionary) {
                $faker = Faker\Factory::create();

                $dictionary->revisions()->saveMany(
                    factory(App\Revision::class, $faker->numberBetween(1, 5))->make()->each(
                        function (App\Revision $revision) use ($faker) {
                            $revision->user_id = $faker->randomElement(App\User::pluck('id')->toArray());
                        }
                    )
                );
                    
                $dictionary->deleted_at = $faker->optional()->dateTime('now', config('timezone'));
                $dictionary->updated_at = $dictionary->revision->created_at;
                $dictionary->save();
            });
    }
}
