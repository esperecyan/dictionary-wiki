<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyConstraintToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('name_provider_id')->references('id')->on('external_accounts');
            $table->foreign('email_provider_id')->references('id')->on('external_accounts');
            $table->foreign('avatar_provider_id')->references('id')->on('external_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_name_provider_id_foreign');
            $table->dropForeign('users_email_provider_id_foreign');
            $table->dropForeign('users_avatar_provider_id_foreign');
        });
    }
}
