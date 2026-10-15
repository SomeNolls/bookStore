const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .vue();

mix.js('resources/js/welcome.js', 'public/js')
    .react();

mix.js('resources/js/header.js', 'public/js')
    .react();

mix.css('resources/css/header.css', 'public/css');
