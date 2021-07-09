import { findIndex, isEqual } from 'lodash'
import wp from '@/utils/wordpress'

const state = () => ({
  all: [],
  activeFacets: [],
  filtered: [],
  facets: null
})

const getters = {}

const mutations = {
  applyFilter (state, filtered) {
    state.filtered = filtered
  },
  clearFacets (state) {
    state.activeFacets = []
    state.filtered = state.all
  },
  empty (state) {
    state.all = []
    state.filtered = []
  },
  init (state, staff) {
    state.all = staff
    state.filtered = staff
  },
  initFacets (state, facets) {
    state.facets = facets
  },
  toggleFacet (state, facet) {
    const facetIndex = findIndex(state.activeFacets, f => isEqual(f, facet))
    if (facetIndex > -1) {
      state.activeFacets.splice(facetIndex, 1)
    } else {
      state.activeFacets.push(facet)
    }
  }
}

const actions = {
  // Fetch all library staff from core CUL instance & prep facets
  async init ({ commit }, preFilter) {
    let staff = await wp.getAllStaff()

    // Additional prep if prefiltering via Wordpress
    // -- supports prefilter by unit and dept
    if (preFilter) {
      const appliedFilters = Object.entries(preFilter).filter(facet => facet[1])
      staff = staff.filter(person => {
        return appliedFilters.every(facet => wp.meetsCriteria(person, facet, true))
      })
    }

    if (staff.length === 0) {
      commit('empty')
    } else {
      const facets = wp.prepFacets(staff, preFilter)

      commit('init', staff.sort((a, b) => a.acf.last_name.localeCompare(b.acf.last_name)))
      commit('initFacets', facets)
    }
  },
  filterStaff ({ commit, state }, facet) {
    commit('toggleFacet', facet)

    if (state.activeFacets.length > 0) {
      const filteredStaff = state.all.filter(person => {
        return state.activeFacets.every(facet => wp.meetsCriteria(person, facet))
      })

      commit('applyFilter', filteredStaff)
    } else {
      commit('clearFacets')
    }
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
