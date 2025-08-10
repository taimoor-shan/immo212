const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/themes/${directory}`
const dist = `public/themes/${directory}`

// Force enable source maps for development
mix.sourceMaps(true, 'source-map')

mix
    .sass(`${source}/assets/sass/style.scss`, `${dist}/css`, {
        sassOptions: {
            outputStyle: 'expanded'
        }
    })
    .js(`${source}/assets/js/script.js`, `${dist}/js`)
    .js(`${source}/assets/js/calendar-frontend.js`, `${dist}/js`)
    .js(`${source}/assets/js/mortgage-calculator.js`, `${dist}/js`)
    .copy(`${source}/assets/css/mortgage-calculator.css`, `${dist}/css`)
    .copy('node_modules/flatpickr/dist/flatpickr.min.css', `${dist}/css/plugins`);

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/style.css`, `${source}/public/css`)
        .copy(`${dist}/js/script.js`, `${source}/public/js`)
        .copy(`${dist}/js/calendar-frontend.js`, `${source}/public/js`)
}
