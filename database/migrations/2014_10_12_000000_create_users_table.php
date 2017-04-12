<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('name_provider_id')->unsigned()->nullable();
            $table->integer('email_provider_id')->unsigned()->nullable();
            $table->integer('avatar_provider_id')->unsigned()->nullable();
            $table->string('profile', User::MAX_PROFILE_LENGTH)->nullable();
            $table->integer('revision_count')->unsigned()->default(0);
            $table->timestamp('revision_created_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
