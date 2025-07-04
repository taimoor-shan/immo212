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

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/style.css`, `${source}/public/css`)
        .copy(`${dist}/js/script.js`, `${source}/public/js`)
}
