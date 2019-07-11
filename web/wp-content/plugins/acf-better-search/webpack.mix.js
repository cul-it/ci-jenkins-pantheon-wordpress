/* ---
  Docs: https://github.com/gbiorczyk/mati-mix/
--- */
const mix = require('mati-mix');

mix.js([
  'resources/_dev/js/Core.js',
], 'public/build/js/scripts.js');

mix.sass(
  'resources/_dev/scss/Core.scss'
, 'public/build/css/styles.css');
