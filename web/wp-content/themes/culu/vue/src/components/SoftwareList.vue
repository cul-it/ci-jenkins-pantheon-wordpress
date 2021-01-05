<template>
  <div>
    <h2>{{ unit }} Library Software Availability</h2>

    <v-simple-table
      fixed-header
      height="100vh"
      class="software-avail"
    >
      <thead>
        <tr>
          <th>Software Name</th>
          <th
            v-for="(value, key) in locations"
            :key="key"
          >
            {{ trimLocation(value) }}
          </th>
        </tr>
      </thead>
      <tbody class="software-list__tbody">
        <tr
          v-for="(software, index) in uniqueSoftware"
          :key="index"
        >
          <td>{{ software.title }}</td>
          <td
            v-for="(entry, index) in locations"
            :key="index"
          >
            <unicon
              v-if="software.locations.findIndex(s => s.includes(entry)) !== -1"
              name="check"
              fill="limegreen"
              width=35
              height=35
            />
          </td>
        </tr>
      </tbody>
      <caption aria-hidden="true">Software availability on Mann Library computers</caption>
    </v-simple-table>
  </div>
</template>

<script>
  export default {
    props: {
      unitData: Array
    },

    computed: {
      locations () {
        return [...new Set(this.unitData.map(software => software.division))].sort()
      },
      uniqueSoftware () {
        return this.mergeSoftware(this.unitData)
      },
      unit () {
        let segments = this.locations[0].split('.')
        return segments[segments.length - 2]
      }
    },

    methods: {
      trimLocation (location) {
        const start = location.lastIndexOf('.') + 1
        return location.substring(start)
      },
      mergeSoftware (softwareArray) {
        const result = []
        const map = new Map()
        for (const item of softwareArray) {
          // Use map to track unique software (via Sassafras product id)
          if (!map.has(item.id)) {
            map.set(item.id, true)
            result.push({
              id: item.id,
              locations: [item.division],
              familyname: item.familyname,
              title: item.title
            })
          // Retain all locations (divisions in Sassafras speak) to power our availability table
          } else {
            const match = result.findIndex(r => r.id === item.id)
            result[match].locations.push(item.division)
          }
        }
        // Alpha sort by software title
        return result.sort((a,b) => a.title.localeCompare(b.title))
      }
    },
  }
</script>

<style scoped>
  [aria-hidden='true'] {
    display: none;
  }

  .software-avail {
    margin-bottom: 3em;
  }
</style>
