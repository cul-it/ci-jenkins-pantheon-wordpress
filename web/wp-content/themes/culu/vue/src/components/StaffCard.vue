<template>
  <li
    :id="person.slug"
    class="staff-card"
    :aria-label="
      person.acf.first_name + ' ' + person.acf.last_name + ' staff profile'
    "
  >
    <!-- Container for profile photo, network links, and contact info.  -->
    <div class="photo-network-contact">
      <!-- Placeholder for staff profile photo, until Distributor plugin pushing images issue is resolved  -->
      <figure class="photo">
        <img
          :src="staffPhoto"
          alt=""
        >
      </figure>
      <ul class="contact">
        <li v-if="person.acf.email">
          <a :href="'mailto:' + person.acf.email">
            <unicon
              name="envelope"
              fill="#017abc"
            /><span class="email">{{
              person.acf.email
            }}</span>
          </a>
        </li>
        <li v-if="person.acf.phone">
          <unicon
            name="mobile-android"
            fill="#017abc"
          />
          <span class="phone">{{ person.acf.phone }}</span>
        </li>
        <li v-if="person.acf.office_location">
          <unicon
            name="location-point"
            fill="#017abc"
          />
          <span class="location">{{ person.acf.office_location }}</span>
        </li>
      </ul>
      <ul class="network">
        <li v-if="person.acf.orcid_id">
          <a
            class="orcid-id"
            :href="person.acf.orcid_id"
          >
            <img
              src="../assets/orcid-id-logo.svg"
              alt="Orcid Logo"
            >
          </a>
        </li>
        <li v-if="person.acf.linkedin_profile">
          <a
            class="linkedin-profile"
            :href="person.acf.linkedin_profile"
          >
            <img
              src="../assets/linkedin-logo.svg"
              alt="LinkedIn logo"
            >
          </a>
        </li>
      </ul>
    </div>

    <!-- Container for profile content -->
    <div class="content">
      <h3 class="name">
        {{ person.acf.first_name }} {{ person.acf.last_name }}<span
          v-if="person.acf.degree"
          class="degree"
        >, {{ person.acf.degree }}</span>
        <span class="title">{{ person.acf.title }}</span>
        <a
          v-if="person.acf.faculty_bio"
          class="bio"
          :href="person.acf.faculty_bio"
        >Professional Biography</a>
      </h3>

      <ul
        v-if="person.acf.departments"
        class="facets"
      >
        <span class="label">Department</span>
        <li
          v-for="department in person.acf.departments"
          :key="department.name"
          class="comma"
        >{{ department.name }}</li> <!-- eslint-disable-line vue/multiline-html-element-content-newline -->
      </ul>
      <ul
        v-if="person.acf.areas_of_expertise"
        class="facets"
      >
        <span class="label">Expertise</span>
        <li
          v-for="expertise in person.acf.areas_of_expertise"
          :key="expertise.name"
          class="comma"
        >{{ expertise.name }}</li> <!-- eslint-disable-line vue/multiline-html-element-content-newline -->
      </ul>
      <ul
        v-if="person.acf.liaison_areas"
        class="facets"
      >
        <span class="label">Liaison Area</span>
        <li
          v-for="liaison in person.acf.liaison_areas"
          :key="liaison.name"
          class="comma"
        >{{ liaison.name }}</li> <!-- eslint-disable-line vue/multiline-html-element-content-newline -->
      </ul>
      <ul
        v-if="person.acf.discipline_support_team"
        class="facets"
      >
        <span class="label">Team</span>
        <li
          v-for="team in person.acf.discipline_support_team"
          :key="team.name"
          class="comma"
        >{{ team.name }}</li> <!-- eslint-disable-line vue/multiline-html-element-content-newline -->
      </ul>
      <ul
        v-if="person.acf.language_of_expertise"
        class="facets"
      >
        <span class="label">Language</span>
        <li
          v-for="language in person.acf.language_of_expertise"
          :key="language.name"
          class="comma"
        >{{ language.name }}</li> <!-- eslint-disable-line vue/multiline-html-element-content-newline -->
      </ul>
      <StaffButtonConsult
        :consult="person.acf.consultation"
        :first-name="person.acf.first_name"
      />
      <p
        v-if="!prefiltered"
        class="label unit"
      >
        {{ unitName }}
      </p>
    </div>
  </li>
</template>

<script>
import StaffButtonConsult from '@/components/StaffButtonConsult.vue'
import util from '@/utils/utils'
import wp from '@/utils/wordpress'

export default {
  components: {
    StaffButtonConsult
  },
  props: {
    person: {
      type: Object,
      default: () => ({})
    },
    prefiltered: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    unitName () {
      return util.capitalCase(this.person.acf.unit_name)
    },
    staffPhoto () {
      return wp.getStaffPhoto(this.person.staff_photo_url)
    }
  }
}
</script>

<style lang="scss">
/* unicon styles */
.unicon {
  color: $color__link;
}

/* Staff card component styles */
.staff-card {
  border: 1px solid #e2eaef;
  position: relative;
  &:hover {
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    border: 0;
  }
  padding: 10px;
  padding-bottom: 0;
  list-style-type: none;
  margin-bottom: 40px;
  color: #6d7278;
  font-family: $font__main;

  /* UI layout */
  display: grid;
  grid-template-columns: 100%;
  grid-template-areas:
    "photo-network-contact"
    "content";
  @media only screen and (min-width: 480px) {
    grid-gap: 1em;
    grid-template-columns: 150px auto;
    grid-template-areas: "photo-network-contact    content";
  }
  @media only screen and (min-width: 640px) {
    grid-template-columns: 205px auto;
  }

  /* Layout for profile photo, network links, and contact info */
  .photo-network-contact {
    grid-area: photo-network-contact;
    padding: 5px;
  }

  /* Layout for staff content */
  .content {
    grid-area: content;
    padding: 5px;
    padding-left: 5px;
    @media only screen and (min-width: 480px) {
      border-left: 1px solid #e2eaef;
      padding-left: 20px;
    }
    .facets {
      margin-bottom: 10px;
    }
  }

  /* Section for profile photo, network links, and contant info. */
  .photo-network-contact {
    .photo {
      margin: 0;
      width: auto;
      img {
        max-width: 200px;
        border-bottom: 15px solid #f3f2f2; //rgba(3, 122, 188, 0.2)
        @media only screen and (min-width: 480px) {
          max-width: 150px;
        }
        @media only screen and (min-width: 640px) {
          max-width: 200px;
        }
      }
    }
    .contact {
      list-style-type: none;
      padding-left: 0;
      padding-bottom: 10px;
      margin-bottom: 0;
      .email {
        font-size: 14px;
        letter-spacing: 0;
        display: inline-block;
        padding-left: 5px;
        &:hover {
          text-decoration: underline;
        }
      }
      .phone {
        font-size: 14px;
        color: #6d7278;
        letter-spacing: 0;
        padding-left: 5px;
      }
      .location {
        font-size: 14px;
        color: #6d7278;
        letter-spacing: 0;
      }
    }
    .network {
      list-style-type: none;
      padding-left: 0;

      li {
        display: inline-block;
      }
      .orcid-id {
        padding-right: 7px;
      }
      /*.linkedin-profile {
      }*/
    }
  }

  hr {
    border: 1px solid #979797;
  }

  /* Section for staff profile content. */
  .name {
    font-size: 22px;
    color: #6d7278;
    letter-spacing: 0;
    font-family: $font__secondary;
    font-weight: bold;
    @media only screen and (min-width: 480px) {
      padding-top: 15px;
    }
    margin-bottom: 20px;
    margin-top: 5px;
    .degree {
      font-size: 18px;
    }
    .title {
      font-weight: normal;
      font-style: italic;
      font-size: 17px;
      display: block;
      padding-top: 5px;
      padding-bottom: 10px;
    }
    .bio {
      font-size: 14px;
      display: block;
      font-family: $font__main;
      text-decoration: none;
      &:hover {
        text-decoration: underline;
      }
    }
  }
  .label {
    display: inline-block;
    line-height: 1;
    vertical-align: baseline;
    margin: 0 0.15em;
    background-color: #e8e8e8;
    padding: 0.5833em 0.833em;
    color: rgba(0, 0, 0, 0.6);
    border: 0 solid transparent;
    border-radius: 0.3rem;
    font-size: 10px;
    margin-right: 10px;
    &.unit {
      position: absolute;
      top: 0;
      right: 0;
      padding: 15px 10px 7px 10px;
      font-weight: bold;
      border-radius: 0;
    }
  }
  .facets {
    padding-left: 0;
    font-weight: bold;
    margin-bottom: 20px;
    li {
      font-weight: normal;
      display: inline-block;
      text-align: left;
      margin-right: 5px;
      font-size: 14px;
      color: #6d7278;
      line-height: 23px;
    }
  }
}
</style>
