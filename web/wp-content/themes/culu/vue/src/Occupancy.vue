<template>
  <div>
    <occupancy-unit
      v-for="(id, index) in sensourceId"
      :id="id"
      :key="id"
      :index="index"
      :is-olin-uris="isOlinUris"
    />
  </div>
</template>

<script>
import OccupancyUnit from '@/components/OccupancyUnit.vue'

export default {
  components: {
    OccupancyUnit
  },
  data () {
    return {
      sensourceId: typeof getSensourceId !== 'undefined' ? getSensourceId : ['469b3905'] // eslint-disable-line no-undef
    }
  },
  computed: {
    isOlinUris () {
      return this.sensourceId.length > 1
    }
  }
}
</script>

<style lang="scss">
.building-occupancy {
  display: grid;
  grid-template-columns: 37px 200px;
  border: 1px solid #017abc;
  border-radius: 3px;
  width: 237px;
  padding: 10px 10px 5px 10px;
  margin: 10px;
  margin-left: 0;

  .occupancy-icon {
    grid-column: 1/2;
    padding-top: 5px;
  }

  .occupancy-info {
    grid-column: 2/3;
    padding-top: 5px;
    padding-right: 10px;
    text-align: center;
    line-height: 1.2em;

    .remaining {
      font-weight: bold;
      font-size: 16px;
    }

    .occupancy {
      font-size: 13px;
    }
  }
}
</style>
