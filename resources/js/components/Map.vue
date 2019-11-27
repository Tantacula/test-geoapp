<template>
  <div class="LMap">
    <LMap ref="lmap"
          style="height: 80vh; width: 100%"
          :zoom="zoom"
          :center="center"
          @update:zoom="zoomUpdated"
          @update:center="centerUpdated"
          @update:bounds="boundsUpdated"
          @ready="mapLoaded"
    >
      <lTileLayer :url="url"/>
    </LMap>
  </div>
</template>

<script>
import axios from 'axios'
import { debounce } from 'lodash-es'
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
    bounds: 'map/bounds',
  }),

  watch: {
    async bounds (val, oldVal) {
      if (val && 'from' in val && 'to' in val) {
        this.updatePlacesList(val.from, val.to)
      }
    },
  },

  methods: {
    zoomUpdated (zoom) {
      this.setZoom({ zoom })
    },
    centerUpdated (center) {
      this.setCenter({ center })
    },
    boundsUpdated (bounds) {
      const { _southWest: from, _northEast: to } = bounds
      this.setBounds({ bounds: { from, to } })
    },
    mapLoaded (mapObject) {
      const { _southWest: pointSW, _northEast: pointNE } = mapObject.getBounds()
      this.setBounds({ bounds: { pointSW, pointNE } })
    },
    updatePlacesList: debounce.call(this, function () {
      this.loadPlacesList()
    }, 400),
    async loadPlacesList () {
      let { from: pointSW, to: pointNE } = this.bounds
      let { data } = await axios.get('/api/places', {
        params: {
          pointSW,
          pointNE,
        },
      })
      // todo: Добавить точки в store
      console.log(data)
    },
    ...mapActions({
      setZoom: 'map/setZoom',
      setCenter: 'map/setCenter',
      setBounds: 'map/setBounds',
    }),
  },
}
</script>

<style lang="scss">
  .LMap {
    height: 100%;
  }
</style>
