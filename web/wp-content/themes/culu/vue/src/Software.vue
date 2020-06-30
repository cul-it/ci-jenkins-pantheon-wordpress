<template>
  <v-app>
    <v-content>
      <SoftwareList/>
    </v-content>
  </v-app>
</template>

<script>
import SoftwareList from './components/SoftwareList'

export default {
  name: 'Software',

  components: {
    SoftwareList,
  },
  
  data: () => ({
    //
  }),
}
</script>
