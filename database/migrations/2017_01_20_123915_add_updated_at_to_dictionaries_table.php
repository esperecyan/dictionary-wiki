<?php

use Illuminate\Database\{Schema\Blueprint, Migrations\Migration};
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Dictionary;

class AddUpdatedAtToDictionariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dictionaries', function (Blueprint $table): void {
            $table->timestamp('updated_at')->nullable()->after('latest');
        });
        
        foreach (Dictionary::withTrashed()->with(['revisions' => function (HasMany $query): void {
            $query->latest();
        }])->get() as $dictionary) {
            $dictionary->updated_at = $dictionary->revisions->first()->created_at;
            $dictionary->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dictionaries', function (Blueprint $table): void {
            $table->dropColumn('updated_at');
        });
    }
}
