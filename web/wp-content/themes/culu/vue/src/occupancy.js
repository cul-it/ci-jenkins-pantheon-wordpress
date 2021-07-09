import Vue from 'vue'
import Occupancy from './Occupancy.vue'
import Unicon from 'vue-unicons'
import { uniSignOutAlt } from 'vue-unicons/src/icons'

Unicon.add([uniSignOutAlt])
Vue.use(Unicon)

Vue.config.productionTip = false

new Vue({
  render: h => h(Occupancy)
}).$mount('#cul-occupancy')
