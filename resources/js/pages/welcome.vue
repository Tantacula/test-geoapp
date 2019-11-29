<template>
  <div>

    <div class="text-center">
      <div class="title mb-4">
        {{ title }}
      </div>

      <p>Здесь вы можете отметить важные места на карте</p>

      <div class="links">
        <template v-if="authenticated">
          <router-link :to="{ name: 'home' }">
            {{ $t('home') }}
          </router-link>
        </template>
        <template v-else>
          <router-link :to="{ name: 'register' }">
            {{ $t('register') }}
          </router-link>
          <router-link :to="{ name: 'login' }">
            {{ $t('login') }}
          </router-link>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'basic',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style scoped>
.title {
  font-size: 85px;
}
</style>
