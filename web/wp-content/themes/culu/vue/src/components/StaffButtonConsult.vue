<template>
  <p
    v-if="consult"
    class="consult"
  >
    <a
      :id="buttonId"
      class="consult-btn"
      href="#"
      :title="linkTitle"
      @click.prevent=""
    >BOOK CONSULTATION</a>

    <!-- Embed LibCal appointments Widget -->
    <script type="application/javascript">
      jQuery.getScript('https://api3.libcal.com/js/myscheduler.min.js', function () {
      jQuery('#{{ buttonId }}').LibCalMySched({
      iid: 973,
      lid: 0,
      gid: 0,
      uid: {{ consult }},
      width: 560,
      height: 680,
      title: 'Make an Appointment - ',
      domain: 'https://api3.libcal.com'
      })
      })
    </script>
  </p>
</template>

<script>
export default {
  props: {
    consult: {
      type: String,
      default: ''
    },
    firstName: {
      type: String,
      default: ''
    }
  },
  computed: {
    buttonId () {
      return `mySched_${this.consult}`
    },
    linkTitle () {
      return `Book a consultation with ${this.firstName}`
    }
  }
}
</script>

<style lang="scss">
.consult {
  .consult-btn {
    background: #037abc;
    font-size: 12px;
    font-weight: bold;
    color: #ffffff;
    letter-spacing: 0;
    text-align: center;
    padding: 10px 13px;
    text-decoration: none;
    display: inline-block;
    &:hover {
      background: $color__link-hover;
    }
  }
}
</style>
