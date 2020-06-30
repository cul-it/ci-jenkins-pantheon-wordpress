module.exports = {
  css: {
    extract: false
  },
  filenameHashing: false,
  pages: {
    software: {
      entry: 'src/software.js'
    }
  },
  productionSourceMap: false,
  transpileDependencies: [
    'vuetify'
  ]
}
