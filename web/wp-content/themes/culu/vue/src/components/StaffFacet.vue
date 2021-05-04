<template>
  <div>
    <h3
      class="facet-name"
      @click.prevent="collapse()"
    >
      <a
        href=""
        :title="formatName"
      >{{ formatName }}
        <unicon
          name="angle-down"
          fill="#017abc"
        />
      </a>
    </h3>
    <ul v-if="!isCollapsed">
      <li
        v-for="value in facet[1]"
        :key="value.name"
        class="facet-value"
      >
        <input
          :id="formatAttribute(facetValue(value))"
          type="checkbox"
          :name="formatAttribute(facetValue(value))"
          :value="facetValue(value)"
          :checked="isActive(facetValue(value, true))"
          @click="filterStaff(facetValue(value, true))"
        >
        <label :for="formatAttribute(facetValue(value))">{{ facetValue(value) }}</label>
      </li>
    </ul>
  </div>
</template>

<script>
import { decode } from 'entities'
import { mapActions } from 'vuex'
import { findIndex, kebabCase, isEqual } from 'lodash'
import util from '@/utils/utils'

export default {
  props: {
    collapsed: Boolean,
    facet: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      isCollapsed: this.collapsed
    }
  },
  computed: {
    formatName () {
      return this.facet[0].replaceAll('_', ' ')
    }
  },
  methods: {
    collapse () {
      this.isCollapsed = !this.isCollapsed
    },
    isActive (facet) {
      // Use Vuex store to drive checked state of facets
      const facetIndex = findIndex(this.$store.state.staff.activeFacets, f => isEqual(f, facet))
      if (facetIndex > -1) {
        return true
      } else {
        return false
      }
    },
    facetValue (value, filter = false) {
      if (typeof value === 'string') {
        // Covers `unit_name`
        if (filter) {
          return [this.facet[0], value]
        } else {
          return util.capitalCase(value)
        }
      } else {
        // Otherwise dealing with an object from WP taxonomy
        return filter ? [this.facet[0], value.term_id, decode(value.name)] : decode(value.name)
      }
    },
    formatAttribute (value) {
      // Prepend value with facet name since some facets share values
      // -- i.e. "Access Services" for both depts & areas of expertise
      return kebabCase(this.facet[0] + '-' + value)
    },
    ...mapActions('staff', [
      'filterStaff'
    ])
  }
}
</script>

<style lang="scss">
.facet,
.facet-name {
  text-transform: capitalize;
  font-size: 18px;
  letter-spacing: 0;
  line-height: 50px;
  font-family: $font__main;
  a {
    text-decoration: none;
    color: $color__link;
    &:hover {
      color: $color__link-hover;
    }
    &:visited {
      color: $color__link-visited;
    }
    &:active {
      color: $color__link-active;
    }
  }
}
.facet-name {
  border-top: 1px solid #e2eaef;
  margin-top: 0;
  margin-bottom: 0;
}
.staff-facets ul {
  padding-left: 0;
  margin-left: 0;
  margin-top: 0;
  li {
    padding-bottom: 10px;
    line-height: 1.5em;
    display: inline-block;
    @media only screen and (min-width: 850px) {
      display: block;
    }
    padding-right: 13px;
    label {
      padding-left: 7px;
    }
    &:last-of-type {
      padding-bottom: 0;
    }
  }
}
.unicon svg {
  vertical-align: middle;
}
</style>
