module.exports = {
  css: {
    extract: false,
    loaderOptions: {
      scss: {
        prependData: '@import "../sass/variables-site/_variables-site.scss"; @import "../sass/mixins/_mixins-master.scss"; @import "../sass/typography/_typography.scss";'
      }
    }
  },
  filenameHashing: false,
  pages: {
    software: {
      entry: 'src/software.js'
    },
    staff: {
      entry: 'src/staff.js'
    },
    occupancy: {
      entry: 'src/occupancy.js'
    }
  },
  productionSourceMap: false,
  // Override the publicPath when compiling via `yarn build`
  // -- to ensure static assets are resolved properly within Wordpress theme
  publicPath: process.env.NODE_ENV === 'production'
    ? '/wp-content/themes/culu/vue/dist/'
    : '/',
  transpileDependencies: [
    'vuetify'
  ]
}
