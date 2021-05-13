import { capitalize } from 'lodash'

export default {
  capitalCase (value) {
    return (value === 'olinuris') ? 'Olin & Uris' : capitalize(value)
  }
}
