import Vue from 'vue'
import Software from './Software.vue'
import Unicon from 'vue-unicons'
import { uniCheck } from 'vue-unicons/src/icons'
import vuetify from './plugins/vuetify'

Unicon.add([uniCheck])
Vue.use(Unicon)

Vue.config.productionTip = false

new Vue({
  vuetify,
  render: h => h(Software)
}).$mount('#cul-software')
