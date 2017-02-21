<?php

use Illuminate\Database\{Schema\Blueprint, Migrations\Migration};
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;
use App\Observers\UserInfomationCacheUpdater;

class AddRevisionCountAndRevisionCreatedAtToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->integer('revision_count')->unsigned()->default(0)->after('profile');
            $table->timestamp('revision_created_at')->nullable()->after('revision_count');
        });
        
        foreach (User::withTrashed()->with(['revisions' => function (HasMany $query): void {
            $query->latest();
        }, 'revisions.dictionary'])->get() as $user) {
            if (isset($user->revisions[0])) {
                $user->revision_created_at = $user->revisions[0]->created_at;
            }
            (new UserInfomationCacheUpdater())->setRevisionCount($user);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['revision_count', 'revision_created_at']);
        });
    }
}
