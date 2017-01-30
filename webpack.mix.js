const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

//mix.js('resources/assets/js/app.js', 'public/js')
//   .sass('resources/assets/sass/app.scss', 'public/css');

mix
	//.sass('resources/assets/sass/font-awesome.scss', 'public/css')
	.copy('bower_components/jquery/dist/jquery.js', 'public/js/')
	//.sass('resources/assets/sass/bootstrap.scss', 'public/css')
	//.sass('resources/assets/sass/bootstrap-theme.scss', 'public/css')
	.copy('bower_components/bootstrap-sass/assets/javascripts/bootstrap.js', 'public/js/')
	.copy('bower_components/bootstrap-validator/dist/validator.js', 'public/js/')
	.copy('bower_components/handsontable/dist/handsontable.full.js', 'public/js/')
	.copy('bower_components/handsontable/dist/handsontable.full.css', 'public/css/')
	.copy('bower_components/handsontable/plugins/bootstrap/handsontable.bootstrap.css', 'public/css/')
	.copy('bower_components/papaparse/papaparse.js', 'public/js/');
