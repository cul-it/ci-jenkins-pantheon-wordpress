<template>
  <v-app>
    <v-content>
      <SoftwareList
        v-for="(value, key) in sassafrasData"
        :key="key"
        :unitData="value"
      />
    </v-content>
  </v-app>
</template>

<script>
import SoftwareList from './components/SoftwareList'
// Only needed in dev
  // TODO: Revisit dev/prod managment and consider dynamic import for this particular case
  // -- https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import#Dynamic_Imports
  if (process.env.NODE_ENV === 'development') {
    var sassafrasDataDev = require('../sassafras-sample.json')
  }
  
export default {
  name: 'Software',

  components: {
    SoftwareList,
  },
  
  data: () => ({
    // Fallback to sample JSON file if serving in dev and not mounted to Wordpress
    sassafrasData: typeof sassafrasDataWP !== 'undefined' ? sassafrasDataWP : sassafrasDataDev // eslint-disable-line no-undef
  }),
}
</script>
