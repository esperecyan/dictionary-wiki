<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * テストタグデータの作成数。
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
        $faker = Faker\Factory::create();
        
        factory(App\Tag::class, static::DATA_LENGTH)->create();
        
        foreach (App\Dictionary::all() as $dictionary) {
            $dictionary->tags()->sync(
                $faker->randomElements(App\Tag::pluck('id')->toArray(), $faker->numberBetween(0, App\Tag::MAX_TAGS))
            );
            $dictionary->revision->tags = $dictionary->tags()->pluck('name');
            $dictionary->revision->save();
        }
    }
}
