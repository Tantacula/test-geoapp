<template>
  <div class="LMap">
    <LMap ref="lmap"
          style="height: 80vh; width: 100%"
          :zoom="zoom"
          :center="center"
          @update:zoom="zoomUpdated"
          @update:center="centerUpdated"
          @update:bounds="boundsUpdated"
    >
      <lTileLayer :url="url"/>
    </LMap>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { LMap, LTileLayer } from 'vue2-leaflet'

export default {
  name: 'Map',

  components: {
    LMap,
    LTileLayer,
  },

  data: () => ({
    url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
  }),

  computed: mapGetters({
    zoom: 'map/zoom',
    center: 'map/center',
  }),

  methods: {
    zoomUpdated (zoom) {
      this.setZoom({ zoom })
    },
    centerUpdated (center) {
      this.setCenter({ center })
    },
    boundsUpdated (bounds) {
      // get points
      // this.bounds = bounds
    },
    ...mapActions({
      setZoom: 'map/setZoom',
      setCenter: 'map/setCenter',
    }),
  },
}
</script>

<style lang="scss">
  .LMap {
    height: 100%;
  }
</style>
