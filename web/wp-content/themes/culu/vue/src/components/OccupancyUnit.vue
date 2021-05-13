<template>
  <div class="occupancy-unit">
    <h4 v-if="isOlinUris">
      {{ unitNames[index] }}
    </h4>
    <div class="building-occupancy">
      <div class="occupancy-icon">
        <unicon
          name="sign-out-alt"
          fill="#017abc"
          height="40px"
          width="40px"
        />
      </div>

      <div class="occupancy-info">
        <span class="remaining">{{ remaining }} people may enter</span>
        <span class="occupancy">
          Occupancy {{ occupancy }} / Max {{ capacity }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    id: {
      type: String,
      default: ''
    },
    isOlinUris: {
      type: Boolean,
      default: false
    },
    index: {
      type: Number,
      default: 0
    }
  },
  data () {
    return {
      capacity: 0,
      occupancy: 0,
      unitNames: ['Olin', 'Uris']
    }
  },
  computed: {
    remaining () {
      return this.capacity - this.occupancy
    }
  },
  created: function () {
    this.getSafespace()
  },
  methods: {
    getSafespace () {
      const baseUrl = 'https://display.safespace.io'
      const spaceCode = this.id
      axios.all([
        axios.get(`${baseUrl}/value/live/${spaceCode}`),
        axios.get(`${baseUrl}/entity/space/hash/${spaceCode}`)
      ])
        .then(axios.spread((occupancy, capacity) => {
          this.occupancy = occupancy.data
          this.capacity = capacity.data.space.maxCapacity
        })
        )
    }
  }
}
</script>

<style lang="scss">
.occupancy-unit h4 {
  margin-bottom: 5px !important;
}
</style>
