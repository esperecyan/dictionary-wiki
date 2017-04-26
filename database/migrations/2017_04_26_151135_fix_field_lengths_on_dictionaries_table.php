<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Dictionary;

class FixFieldLengthsOnDictionariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dictionaries', function (Blueprint $table) {
            $table->string('title', Dictionary::MAX_FIELD_LENGTH)->change();
            $table->string('summary', Dictionary::MAX_FIELD_WITH_MARKUP_LENGTH)->nullable()->change();
            $table->string('regard', Dictionary::MAX_FIELD_LENGTH)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dictionaries', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('summary')->nullable()->change();
            $table->string('regard')->nullable()->change();
        });
    }
}
