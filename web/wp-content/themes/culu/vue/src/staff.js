import Vue from 'vue'
import VueForceNextTick from 'vue-force-next-tick'
import Staff from './Staff.vue'
import store from './store'
import Unicon from 'vue-unicons'
import {
  uniAngleDown,
  uniAngleUp,
  uniDocumentLayoutLeft,
  uniEnvelope,
  uniLocationPoint,
  uniListUl,
  uniMobileAndroid,
  uniSort,
  uniTimes
} from 'vue-unicons/src/icons'

Unicon.add([
  uniAngleDown,
  uniAngleUp,
  uniDocumentLayoutLeft,
  uniEnvelope,
  uniLocationPoint,
  uniListUl,
  uniMobileAndroid,
  uniSort,
  uniTimes
])
Vue.use(Unicon)
Vue.use(VueForceNextTick)

Vue.config.productionTip = false

new Vue({
  store,
  render: (h) => h(Staff)
}).$mount('#cul-staff')
