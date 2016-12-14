<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\File as FileModel;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dictionary_id')->unsigned();
            $table->foreign('dictionary_id')->references('id')->on('dictionaries');
            $table->bigInteger('revision_id')->unsigned();
            $table->foreign('revision_id')->references('id')->on('revisions');
            $table->string('type');
            $table->string('name', FileModel::MAX_FILENAME_LENGTH_WITH_EXTENSION);
            $table->unique(['dictionary_id', 'name']);
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
        foreach (Storage::directories(FileModel::DIRECTORY_NAME) as $directory) {
            Storage::deleteDirectory($directory);
        }
        Schema::drop('files');
    }
    
    /**
     * 隠しファイル (ドットファイル) を残して、ディレクトリを空にします。
     *
     * @param string $directory
     * @return bool
     */
    protected function cleanDirectoryExceptForHiddenFiles($directory): bool
    {
        return File::delete(File::allFiles($directory));
    }
}
