<template>
  <li
    :id="person.slug"
    class="staff-card simple"
    :aria-label="
      person.acf.first_name + ' ' + person.acf.last_name + ' staff profile'
    "
  >
    <!-- Placeholder for staff profile photo, until Distributor plugin pushing images issue is resolved  -->
    <figure class="photo">
      <img
        :src="staffPhoto"
        alt=""
      >
    </figure>

    <!-- Container for profile content -->
    <h3 class="name">
      {{ person.acf.first_name }} {{ person.acf.last_name }},
      <span class="degree">{{ person.acf.degree }}</span>
      <span class="title">{{ person.acf.title }}</span>
      <a
        v-if="person.acf.faculty_bio"
        class="bio"
        :href="person.acf.faculty_bio"
      >Professional Biography</a>
    </h3>

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
      <p
        v-if="!prefiltered"
        class="label unit"
      >
        {{ unitName }}
      </p>
    </ul>
  </li>
</template>

<script>
import util from '@/utils/utils'
import wp from '@/utils/wordpress'

export default {
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

/* Staff card component styles */
.staff-card.simple {
  width: 100%;
  border-top-left-radius: 0;
  position: relative;
  line-height: 0;

  /* UI layout */
  display: block;
  margin-right: 20px;
  padding: 0;
  margin-bottom: 20px;
  @media only screen and (min-width: 480px) {
    display: grid;
    grid-template-columns: 100px auto;
    grid-template-areas:
      "photo  content"
      "photo contact";
  }
  @media only screen and (min-width: 640px) {
    display: inline-block;
  }
  .photo {
    margin: 0;
    display: inline-block;
    @media only screen and (min-width: 480px) {
      grid-area: photo;
    }
    @media only screen and (min-width: 640px) {
      display: inline-block;
    }
    img {
      width: 200px;
      //rgba(3, 122, 188, 0.2);
      border-bottom: 10px solid #f3f2f2; //rgba(3, 122, 188, 0.2)
      @media only screen and (min-width: 480px) {
        width: 100px;
      }
    }
  }

  /* Section for staff profile content. */
  .name {
    display: inline-block;
    font-size: 18px;
    margin: 0;
    vertical-align: top;
    padding: 30px 10px 0 20px;
    width: 100%;
    line-height: 1.3em;
    padding-top: 10px;
    @media only screen and (min-width: 480px) {
      grid-area: content;
      width: 300px;
      padding: 30px 10px 0 20px;
    }
    @media only screen and (min-width: 640px) {
      display: inline-block;
    }
    .degree {
      font-size: 15px;
    }
    .title {
      font-size: 16px;
      padding-top: 0;
      padding-bottom: 0;
    }
  }
  .contact {
    display: inline-block;
    @media only screen and (min-width: 480px) {
      grid-area: contact;
    }
    @media only screen and (min-width: 640px) {
      display: inline-block;
      padding-top: 30px;
    }
    padding: 0 10px 10px 10px;
    margin-bottom: 0;
    vertical-align: top;
    padding-top: 10px;
    padding-left: 15px;
    li {
      padding-bottom: 7px;
      display: block;
    }
    .email {
      font-size: 14px;
      padding-left: 5px;
      margin-right: 10px;
    }
    .phone {
      font-size: 14px;
      color: #6d7278;
      letter-spacing: 0;
      vertical-align: middle;
      padding-left: 0;
      margin-right: 10px;
    }
    .location {
      font-size: 14px;
      color: #6d7278;
      letter-spacing: 0;
      vertical-align: middle;
      padding-left: 0;
      margin-right: 10px;
    }
  }
}
</style>
