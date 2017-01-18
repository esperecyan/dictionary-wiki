const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.config.sourcemaps = false;

elixir(mix => {
	//mix.sass('app.scss')
	//	.webpack('app.js');
	
	mix
		.sass('font-awesome.scss')
		.copy('bower_components/jquery/dist/jquery.js', 'public/js/')
		.sass('bootstrap.scss')
		.sass('bootstrap-theme.scss')
		.copy('bower_components/bootstrap-sass/assets/javascripts/bootstrap.js', 'public/js/')
		.copy('bower_components/bootstrap-validator/dist/validator.js', 'public/js/')
		.copy('bower_components/handsontable/dist/handsontable.full.js', 'public/js/')
		.copy('bower_components/handsontable/dist/handsontable.full.css', 'public/css/')
		.copy('bower_components/handsontable/plugins/bootstrap/handsontable.bootstrap.css', 'public/css/')
		.copy('bower_components/papaparse/papaparse.js', 'public/js/');
});
