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
      <LMarker
        v-for="place in places"
        :key="place.id"
        :lat-lng="[place.point.lat, place.point.lng]"
      />
      <lTileLayer :url="url"/>
    </LMap>
  </div>
</template>

<script>
import axios from 'axios'
import { debounce } from 'lodash-es'
import { mapGetters, mapActions } from 'vuex'
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet'

export default {
  name: 'Map',

  components: {
    LMap,
    LTileLayer,
    LMarker,
  },

  data: () => ({
    url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
  }),

  computed: mapGetters({
    zoom: 'map/zoom',
    center: 'map/center',
    bounds: 'map/bounds',
    places: 'map/places',
  }),

  watch: {
    bounds (val, oldVal) {
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
      let { data: { data: places } } = await axios.get('/api/places', {
        params: {
          pointSW,
          pointNE,
        },
      })
      this.setPlaces({ places })
    },
    async sendPointToServer ({ lat, lng, categories, comment }) {
      const { data: { place } } = await axios.post('/api/places', {
        lat,
        lng,
        categories,
        comment,
      })
      this.addPlace({ place })
    },
    ...mapActions({
      setZoom: 'map/setZoom',
      setCenter: 'map/setCenter',
      setBounds: 'map/setBounds',
      setPlaces: 'map/setPlaces',
      addPlace: 'map/addPlace',
    }),
  },
}
</script>

<style lang="scss">
  .LMap {
    height: 100%;
  }
</style>
