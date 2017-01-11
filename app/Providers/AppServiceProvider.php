<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use App\{Dictionary, User};
use App\Observers\ModelCategoryCreator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('markdown', 'App\\Validators\\MarkdownValidator@validate');
        Validator::extend('dictionary_file', 'App\\Validators\\DictionaryFileValidator@validate');
        Validator::extend('dictionary', 'App\\Validators\\DictionaryValidator@validate');
        Validator::extend('external_account', 'App\\Validators\\ExternalAccountValidator@validate');
        
        Dictionary::observe(ModelCategoryCreator::class);
        User::observe(ModelCategoryCreator::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
