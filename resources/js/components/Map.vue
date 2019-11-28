<template>
  <div class="LMap container-fluid">
    <div class="row">
      <div class="col-12 col-lg-8 mb-4">
        <card title="Карта">
          <LMap ref="lmap"
                style="height: 80vh; width: 100%"
                :zoom="zoom"
                :center="center"
                @update:zoom="zoomUpdated"
                @update:center="centerUpdated"
                @update:bounds="boundsUpdated"
                @ready="mapLoaded"
                @click="mapClick"
          >
            <LMarker
              v-for="place in selectedPlaces"
              :key="place.id"
              :lat-lng="[place.point.lat, place.point.lng]"
            >
              <LPopup v-if="place.comment !== null" :content="place.comment"/>
            </LMarker>
            <LTileLayer :url="url"/>
          </LMap>
        </card>
      </div>

      <div class="col-12 col-lg-4">
        <card title="Категории">
          <div
            v-for="category in categories"
            :key="category.id"
            class="custom-control custom-checkbox d-flex"
          >
            <input
              :id="'MapCategory-' + category.id"
              v-model="selectedCategoryIds"
              :value="category.id"
              class="custom-control-input"
              type="checkbox"
            />
            <label :for="'MapCategory-' + category.id" class="custom-control-label my-auto">{{ category.name }}</label>
          </div>
        </card>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { debounce, intersection } from 'lodash-es'
import { mapGetters, mapActions } from 'vuex'
import { LMap, LTileLayer, LMarker, LPopup } from 'vue2-leaflet'

export default {
  name: 'Map',

  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
  },

  data: () => ({
    url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
    selectedCategoryIds: [],
  }),

  computed: {
    selectedCategories () {
      return this.categories.filter(cat => this.selectedCategoryIds.includes(cat.id))
    },
    selectedPlaces () {
      return this.places.filter(place => {
        let catIds = place.categories.map(cat => cat.id);
        return intersection(this.selectedCategoryIds, catIds).length > 0
      })
    },
    ...mapGetters({
      zoom: 'map/zoom',
      center: 'map/center',
      bounds: 'map/bounds',
      places: 'map/places',
      categories: 'map/categories',
    }),
  },

  watch: {
    bounds (val, oldVal) {
      if (val && 'pointNE' in val && 'pointSW' in val) {
        this.updatePlacesList()
      }
    },
    categories (val, oldVal) {
      this.fillSelectedCategories()
    },
  },

  created () {
    if (this.categories.length === 0) {
      this.fetchCategories()
    } else {
      this.fillSelectedCategories()
    }
  },

  methods: {
    zoomUpdated (zoom) {
      this.setZoom({ zoom })
    },
    centerUpdated (center) {
      this.setCenter({ center })
    },
    boundsUpdated (bounds) {
      const { _southWest: pointSW, _northEast: pointNE } = bounds
      this.setBounds({ bounds: { pointSW, pointNE } })
    },
    mapLoaded (mapObject) {
      const { _southWest: pointSW, _northEast: pointNE } = mapObject.getBounds()
      this.setBounds({ bounds: { pointSW, pointNE } })
    },
    mapClick (e) {
      console.log('map click', e)
    },
    updatePlacesList: debounce.call(this, function () {
      this.loadPlacesList()
    }, 400),
    async loadPlacesList () {
      let { pointSW, pointNE } = this.bounds
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
    fillSelectedCategories () {
      this.selectedCategoryIds = this.categories.map(el => el.id)
    },
    ...mapActions({
      setZoom: 'map/setZoom',
      setCenter: 'map/setCenter',
      setBounds: 'map/setBounds',
      setPlaces: 'map/setPlaces',
      addPlace: 'map/addPlace',
      fetchCategories: 'map/fetchCategories',
    }),
  },
}
</script>

<style lang="scss">
  .LMap {
    height: 100%;
  }
</style>
