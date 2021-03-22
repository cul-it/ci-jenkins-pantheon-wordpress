<template>
  <button
    class="filter__toggle filter__toggle-facet"
    @click="filterStaff(filter)"
  >
    {{ filterLabel }}

    <unicon
      name="times"
      fill="#017abc"
    />
  </button>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  props: {
    filter: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    filterLabel () {
      const filter = this.filter[0].replaceAll('_', ' ')
      // For taxonomy terms, term_id is second array element and term_name is third element
      const value = typeof this.filter[1] === 'number' ? this.filter[2] : this.filter[1]
      return `${filter}: ${value}`
    }
  },
  methods: {
    ...mapActions('staff', [
      'filterStaff'
    ])
  }
}
</script>

<style lang="scss">
.filter__toggles {
  padding-top: 20px;
  .filter__toggle {
    background: #e7eaee;
    font-size: 12px;
    color: #6d7278;
    letter-spacing: 0;
    text-align: center;
    border: 0;
    padding: 7px 10px;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 10px;
    cursor: pointer;
  }
  .filter__toggle-clear {
    margin-right: 10px;
  }
  .filter__toggle-facet {
    margin-right: 10px;
    padding: 5px 10px 2px 10px;
    .unicon {
    }
    svg {
      width: 16px;
      display: inline-block;
      //margin-top: -2px;
    }
  }
}
</style>
