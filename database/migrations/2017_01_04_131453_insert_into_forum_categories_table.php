<?php

use App\{Dictionary, User};
use App\Http\Controllers\Forum\CategoriesController;
use Illuminate\Database\Migrations\Migration;
use Riari\Forum\Models\Category;

class InsertIntoForumCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ([
            [
                'id' => Dictionary::PARENT_FORUM_CATEGORY_ID,
                'title' => parameter_name(Dictionary::class),
                'enable_threads' => false,
            ],
            [
                'id' => User::PARENT_FORUM_CATEGORY_ID,
                'title' => parameter_name(User::class),
                'enable_threads' => false,
            ],
            [
                'id' => CategoriesController::SITE_FORUM_CATEGORY_ID,
                'title' => CategoriesController::SITE_FORUM_CATEGORY_TITLE,
                'enable_threads' => true,
            ],
        ] as $values) {
            Category::create($values);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Category::truncate();
    }
}
