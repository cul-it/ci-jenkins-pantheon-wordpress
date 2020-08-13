<template>
    <v-simple-table fixed-header height="100vh">
      <thead>
        <tr>
          <th
            v-for="(value, key) in softwareList[0]"
            :key="key"
          >
            <a
              v-if="smartmapQuery(key)"
              :href="smartmapQuery(key)"
              title="Map it"
              target="_blank">
                {{ key }}
            </a>
            <span v-else>{{ key }}</span>
          </th>
        </tr>
      </thead>
      <tbody class="software-list__tbody">
        <tr
          v-for="(software, index) in softwareList"
          :key="index"
        >
          <td
            v-for="(entry, index) in software"
            :key="index"
          >
            <unicon
              v-if="entry === 'X'"
              name="check"
              fill="limegreen"
              width=35
              height=35
            />
            <span v-else="">{{ entry }}</span>
          </td>
        </tr>
      </tbody>
      <caption aria-hidden="true">Software availability on Mann Library computers</caption>
    </v-simple-table>
</template>

<script>
  import axios from 'axios'
  import csv from 'csvtojson'

  export default {
    data () {
      return {
        softwareList: []
      }
    },

    methods: {
      // Convert software location names to working SmartMap links
      smartmapQuery (location) {
        const baseUrl = 'http://smartmap.mannlib.cornell.edu/location/'
        if (location.indexOf('iMacs') !== -1 || location.indexOf('Research') !== -1) {
          return baseUrl + 'stone computing center'
        } else if (location.indexOf('PCs') !== -1) {
          return baseUrl + location.replace('PCs', '').trim().toLowerCase()
        } else if (location.indexOf('Circ') !== -1) {
          return baseUrl + 'circulation services'
        } else {
          return false
        }
      }
    },

    mounted () {
      axios
        .get('https://raw.githubusercontent.com/cul-it/mann-softwarelist-csv/master/softwarelist.csv')
        .then(response => csv().fromString(response.data))
        .then((jsonObj) => this.softwareList = jsonObj)
    },
  }
</script>

<style>
  [aria-hidden='true'] {
    display: none;
  }
</style>
