<?php

use Illuminate\Database\{Schema\Blueprint, Migrations\Migration};
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;

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
        }])->get() as $user) {
            $user->revision_count = count($user->revisions);
            if ($user->revision_count > 0) {
                $user->revision_created_at = $user->revisions[0]->created_at;
            }
            $user->save();
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
