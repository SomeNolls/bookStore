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

mix.js('resources/js/site.js', 'public/js')
    .react();

mix.styles([
    'resources/css/base.css',
    'resources/css/header.css',
    'resources/css/banner.css',
    'resources/css/sections.css',
    'resources/css/catalogue.css',
    'resources/css/book.css',
    'resources/css/footer.css',
], 'public/css/site.css');
