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

elixir(mix => {
	//mix.sass('app.scss')
	//	.webpack('app.js');
	
	mix.copy('node_modules/handsontable/dist/handsontable.full.js', 'public/js/')
		.copy('node_modules/handsontable/dist/handsontable.full.css', 'public/css/')
		.copy('node_modules/handsontable/plugins/bootstrap/handsontable.bootstrap.css', 'public/css/')
		.copy('node_modules/papaparse/papaparse.js', 'public/js/')
		.copy('node_modules/bootstrap-validator/dist/validator.js', 'public/js/');
});
