<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\{Revision, File, Tag};

class CreateRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('dictionary_id')->unsigned();
            $table->foreign('dictionary_id')->references('id')->on('dictionaries');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->longText('data');
            $table->string('tags', (Tag::MAX_LENGTH + 3) * Tag::MAX_TAGS + 1);
            $table->mediumText('files');
            $table->string('summary', Revision::MAX_SUMMARY_LENGTH);
            $table->string('ipaddr', Revision::MAX_IPADDR_LENGTH);
            $table->mediumText('external_accounts');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('revisions');
    }
}
