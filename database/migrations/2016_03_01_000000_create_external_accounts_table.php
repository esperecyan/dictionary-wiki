<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provider', 91);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['provider', 'user_id']);
            $table->string('provider_user_id', 100);
            $table->unique(['provider', 'provider_user_id']);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->string('link')->nullable();
            $table->boolean('public')->default(true);
            $table->boolean('available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('external_accounts');
    }
}
