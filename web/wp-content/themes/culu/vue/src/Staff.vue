<template>
  <div
    role="region"
    aria-label="Staff Directory"
  >
    <UtilsLoader
      v-if="loading"
    />

    <template v-else>
      <section class="staff">
        <div
          class="staff-facets"
          role="region"
          aria-label="Staff Facets"
        >
          <form
            class="search-names"
          >
            <label for="search-by-name">Search by name: </label>
            <input
              id="search-by-name"
              v-model="searchText"
              name="search-by-name"
              type="search"
            >
          </form>
          <div class="filter__toggles">
            <button
              v-if="activeFacets.length > 0"
              class="filter__toggle filter__toggle-clear"
              @click="clearFacets()"
            >
              Start Over
            </button>

            <StaffFilter
              v-for="filter in activeFacets"
              :key="filter[1]"
              :filter="filter"
            />
          </div>
          <StaffFacet
            v-for="facet in facets"
            :key="facet[0]"
            :facet="facet"
            :collapsed="true"
          />
        </div>

        <div
          class="staff-cards"
          role="region"
          aria-label="Staff Card Profile"
        >
          <div class="staff-count-tools">
            <h4 class="staff-count">
              <span>{{ searchedStaff.length }}</span> staff members
            </h4>

            <select
              aria-labelledby="sorty"
              class="sort-name"
              @change="sortStaff($event)"
            >
              <option
                id="sorty"
                disabled
                value=""
              >
                Sort By
              </option>
              <option
                value="last_name"
                selected
              >
                Last name
              </option>
              <option value="first_name">
                First Name
              </option>
            </select>
            <ul class="view-type">
              <li>
                <a
                  href="#"
                  title="Card view"
                  @click.prevent="setView('card')"
                >
                  <unicon
                    name="document-layout-left"
                    :style="{'fill' : (activeViewIcon? '#6d7278' : '#037abc' )}"
                  />
                </a>
              </li>

              <li>
                <a
                  href="#"
                  title="Detail card view"
                  @click.prevent="setView('simple')"
                >
                  <unicon
                    name="apps"
                    :style="{'fill' : (activeViewIcon? '#037abc' : '#6d7278' )}"
                  />
                </a>
              </li>
            </ul>
          </div>

          <ul>
            <component
              :is="activeView"
              v-for="person in searchedStaff"
              :key="person.id"
              :person="person"
            />
          </ul>
        </div>
      </section>
    </template>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import StaffCard from '@/components/StaffCard.vue'
import StaffFacet from '@/components/StaffFacet.vue'
import StaffSimple from '@/components/StaffSimple.vue'
import StaffFilter from '@/components/StaffFilter.vue'
import UtilsLoader from '@/components/UtilsLoader.vue'

export default {
  components: {
    StaffCard,
    StaffFacet,
    StaffSimple,
    StaffFilter,
    UtilsLoader
  },
  data () {
    return {
      activeView: 'StaffCard',
      activeViewIcon: true,
      loading: true,
      // Receive prefiltering from Wordpress
      prefiltered: typeof staffFilters !== 'undefined' ? staffFilters : false, // eslint-disable-line no-undef
      searchText: ''
    }
  },
  computed: {
    ...mapState({
      activeFacets: state => state.staff.activeFacets,
      facets: state => state.staff.facets,
      filteredStaff: state => state.staff.filtered,
      staff: state => state.staff.all
    }),
    searchedStaff () {
      const needle = new RegExp(this.searchText, 'i')
      return this.filteredStaff
        .filter(person => person.acf.first_name.match(needle) || person.acf.last_name.match(needle))
    }
  },
  async created () {
    // Initialize Vuex store for state management
    await this.$store.dispatch('staff/init', this.prefiltered)
    this.loading = false
  },
  mounted () {
    // Import jQuery from Google CDN =(
    // -- dependency for LibCal Consultation (my scheduler) widget
    const jqueryCdn = document.createElement('script')
    jqueryCdn.setAttribute('src', '//ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js')
    document.head.appendChild(jqueryCdn)
  },
  methods: {
    ...mapMutations('staff', [
      'clearFacets'
    ]),
    setView (type) {
      this.activeView = (type === 'card') ? 'StaffCard' : 'StaffSimple'
      this.activeViewIcon = !this.activeViewIcon
    },

    sortStaff (event) {
      this.filteredStaff.sort((a, b) => a.acf[[event.target.value]].localeCompare(b.acf[[event.target.value]]))
    }
  }
}
</script>

<style lang="scss">
h2 {
  font-family: $font__secondary;
  font-weight: bold;
  padding-top: 30px;
  padding-bottom: 20px;
  width: calc(100vw - 30px);
  margin: 0 auto;
  @media only screen and (min-width: 850px) {
    width: calc(100vw - 100px);
  }
  @media only screen and (min-width: 1240px) {
    width: 1240px;
  }
}
// ###
a,
button {
  color: $color__link;
  text-decoration: underline;

  &:visited {
    color: $color__link-visited;
  }
  &:hover,
  &:focus,
  &:active {
    color: $color__link-hover;
    text-decoration: none;
  }
  &:focus {
    //outline: thin dotted;
    outline: 1px dashed black;
  }
  &:hover,
  &:active {
    outline: 0;
  }
}
ul {
  padding-left: 0;
  list-style-type: none;
  li {
    color: $color__link;
    font-family: $font__main;
  }
}
// ###
.staff {
  /* Layout */
  display: grid;
  grid-gap: 3em;
  grid-template-columns: auto;
  grid-template-areas:
    "facets"
    "results";
  width: calc(100vw - 60px);
  margin: 0 auto;
  @media only screen and (min-width: 850px) {
    grid-template-columns: 300px auto;
    grid-template-areas: "facets    results";
    width: calc(100vw - 100px);
  }
  @media only screen and (min-width: 1240px) {
    width: 100%;
  }

  .staff-facets {
    grid-area: facets;
  }

  .staff-cards {
    grid-area: results;
    ul {
      margin: 0;
      padding-left: 0;
    }
  }

  .search-names {
    font-weight: bold;
    input {
      border-radius: 0;
      border: 1px solid $color__link;
      //width: 90%;
      width: 100%;
      height: 35px;
    }
  }

  /* UI layout for staff count, sort, and view type */
  .staff-count-tools {
    display: grid;
    grid-gap: 1em;
    grid-template-columns: 200px auto;
    grid-template-areas:
      "staff-count view-type"
      "sort-name sort-name";
    width: 100%;
    @media only screen and (min-width: 972px) {
      grid-template-columns: auto 140px 100px;
      grid-template-areas: "staff-count sort-name view-type";
    }
    .staff-count {
      span {
        font-size: 24px;
        color: $color__link;
      }
      grid-area: staff-count;
      font-family: $font__main;
      margin: 0;
      width: auto;
    }
    .sort-name {
      grid-area: sort-name;
      width: auto;
      display: inline-block;
      margin-left: 0;
      padding-left: 0;
      border: 1px solid #e2eaef;
      padding: 5px 20px 5px 10px;
      border-radius: 0;
      margin: 0;
      margin-bottom: 20px;
      font-family: $font__main;
    }
    .view-type {
      grid-area: view-type;
      padding-top: 3px;
      margin: 0;
      padding-left: 0;
      text-align: right;
      li {
        display: inline-block;
        padding-right: 10px;
      }
    }
  }
  .comma {
    &:after {
      content: ",";
    }
    &:last-of-type:after {
      content: "";
    }
  }
}
</style>
