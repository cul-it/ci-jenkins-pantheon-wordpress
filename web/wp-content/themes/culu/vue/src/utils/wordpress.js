import ky from 'ky'
import { remove, uniqBy } from 'lodash'

const baseUrl = 'https://live-uls-library-cornell-edu.pantheonsite.io/wp-json/wp/v2'

export default {
  api: {
    endpoints: {
      staff: `${baseUrl}/staff?per_page=100`
    }
  },
  extractUnique (staff, facet) {
    // Straightforward map and sort if facet value is a string (i.e. unit_name)
    if (typeof staff[0].acf[facet] === 'string') {
      var extracted = [...new Set(staff.map(person => person.acf[facet]))].sort()
    // But when dealing with a WP taxonomy, facet value is multidimensional (MD) array
    } else {
      extracted = uniqBy([].concat(...staff.map(person => person.acf[facet])), 'term_id') // Flatten MD array
        .filter(Boolean) // Prune `false` (staff with no terms applied from this terminology)
        .sort((a, b) => a.name.localeCompare(b.name)) // Alpha sort by term name
    }
    return extracted.length > 0 ? extracted : false
  },
  async getAllStaff () {
    return await ky.get(this.api.endpoints.staff).json()
  },
  getStaffPhoto (url) {
    // Wrap the file path in require() since v-bind is executed at runtime
    // -- https://github.com/vuejs/vue-loader/issues/896#issuecomment-316697682
    const defaultPhoto = require('../assets/no-photo-profile.png')
    try {
      return url[0] || defaultPhoto
    } catch (error) {
      // Avoid error when accessing first item in an undefined array
      // -- will be encountered in dev prior to staff_photo_url rollout to uls-library prod
      return defaultPhoto
    }
  },
  meetsCriteria (person, facet, preFilter = false) {
    const personValue = person.acf[facet[0]]

    if (typeof personValue === 'string') {
      // A straightforward filter on string equality (i.e. unit_name)
      return personValue === facet[1]
    } else if (Array.isArray(personValue)) {
      // When prefiltering via Wordpress, match on taxonomy term slug
      if (preFilter) {
        const termSlugs = [...new Set(personValue.map(term => term.slug))]

        return termSlugs.includes(facet[1])
      }
      // Otherwise, matching on taxonomy term ID so boil down to unique term IDs via Map/Set combo
      const termIds = [...new Set(personValue.map(term => term.term_id))]

      return termIds.includes(facet[1])
    } else {
      return false
    }
  },
  prepFacets (staffData, preFilter) {
    const facets = new Map()
    const facetList = [
      'unit_name',
      'departments',
      'areas_of_expertise',
      'liaison_areas',
      'discipline_support_team',
      'language_of_expertise'
    ]

    // Drop facets that are applied via prefiltering from Wordpress
    if (preFilter) {
      // First prune the incoming list to remove dept if empty (default value)
      const preApplied = Object.keys(preFilter).filter(facet => preFilter[facet])
      remove(facetList, facet => preApplied.includes(facet))
    }

    facetList.forEach(facet => {
      const uniqueValues = this.extractUnique(staffData, facet)

      // Only include applicable facets
      if (uniqueValues) {
        facets.set(facet, uniqueValues)
      }
    })

    return facets
  }
}
