<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Dictionary;

class CreateDictionariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dictionaries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
            $table->string('locale', Dictionary::MAX_LOCALE_LENGTH);
            $table->string('title');
            $table->integer('words');
            $table->string('summary')->nullable();
            $table->string('regard')->nullable();
            $table->binary('latest');
            $table->timestamp('updated_at');
            $table->softDeletes();
        });
        if (DB::getDefaultConnection() === 'mysql') {
            DB::statement('ALTER TABLE dictionaries CHANGE COLUMN latest latest LONGBLOB');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dictionaries');
    }
}
