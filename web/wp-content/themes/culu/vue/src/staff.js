import Vue from 'vue'
import Staff from './Staff.vue'
import store from './store'
import Unicon from 'vue-unicons'
import {
  uniEnvelope,
  uniLocationPoint,
  uniMobileAndroid,
  uniAngleDown,
  uniAngleUp,
  uniApps,
  uniDocumentLayoutLeft,
  uniSort,
  uniTimes
} from 'vue-unicons/src/icons'

Unicon.add([uniEnvelope, uniLocationPoint, uniMobileAndroid, uniAngleDown, uniAngleUp, uniApps, uniDocumentLayoutLeft, uniSort, uniTimes])
Vue.use(Unicon)

Vue.config.productionTip = false

new Vue({
  store,
  render: (h) => h(Staff)
}).$mount('#cul-staff')
