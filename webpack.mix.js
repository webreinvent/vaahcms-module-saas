const mix = require('laravel-mix');
var fs = require('fs');
const fs_extra = require('fs-extra');



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

let publish_folder;
let output_folder;
let source_vue_folder;

source_vue_folder = __dirname+'/Vue';


if (mix.inProduction()) {
    /*
     |--------------------------------------------------------------------------
     | Only in Production
     |--------------------------------------------------------------------------
     */
    console.log('---------------------');
    console.log('IN PRODUCTION MODE');
    console.log('---------------------');

    publish_folder = './Resources/assets/';
    output_folder = "./";

    mix.setPublicPath(publish_folder);
    mix.js(source_vue_folder+"/app.js",  output_folder+'/build/app.js');

} else {

    publish_folder = './../../../public/vaahcms/modules/';
    output_folder = "./saas/assets/";

    mix.setPublicPath(publish_folder);

    mix.js(source_vue_folder+"/app.js",  output_folder+'/build/app.js');

}


mix.webpackConfig({
    watchOptions: {
        aggregateTimeout: 2000,
        poll: 20,
        ignored: [
            '/Config/',
            '/Database/',
            '/Entities/',
            '/Helpers/',
            '/Http/',
            '/Providers/',
            '/Resources/',
            '/Routes/',
            '/node_modules/',
            '/vendor/',

        ]
    }
});
