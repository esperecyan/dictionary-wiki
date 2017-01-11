<?php

use App\{Dictionary, User};
use Illuminate\Database\Migrations\Migration;

class InsertExistingDictionariesAndUsersIntoForumCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ([Dictionary::class, User::class] as $className) {
            $className::getParentForumCategory()->categories()->createMany(array_map(function (int $id): array {
                return ['title' => $id, 'enable_threads' => true];
            }, $className::withTrashed()->pluck('id')->toArray()));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ([Dictionary::class, User::class] as $className) {
            $className::getParentForumCategory()->categories()->delete();
        }
    }
}
