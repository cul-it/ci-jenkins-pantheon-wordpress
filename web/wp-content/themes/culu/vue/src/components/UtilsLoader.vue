<template>
  <span class="loading">
    <img
      src="../assets/loader.svg"
      alt=""
    > Loading ...
  </span>
</template>

<script>
export default {}
</script>

<style lang="scss">
.loading {
  display: block;
  margin: 0 auto;
  text-align: center;
  color: #017abc;
  img {
    width: 100px;
    height: 100px;
    vertical-align: middle;
    margin-right: -20px;
  }
}
</style>
