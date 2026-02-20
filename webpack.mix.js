const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 | Convert Vite-built pipeline to Laravel Mix (Webpack). Outputs to public/js and public/css.
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css', { implementation: require('sass') })
   .options({
       postCss: [ require('tailwindcss'), require('autoprefixer') ],
   })
   .sourceMaps(false)
   .version();
