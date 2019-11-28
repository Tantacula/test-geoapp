<template>
    <Map/>
</template>

<script>
import Map from '~/components/Map';

export default {
  middleware: 'auth',

  components: {
    Map,
  },

  metaInfo () {
    return { title: this.$t('home') }
  },
}
</script>
