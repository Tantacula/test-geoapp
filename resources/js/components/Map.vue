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
                @click="mapClickHandler"
          >
            <MapMarker
              v-for="place in selectedPlaces"
              :key="place.id"
              :place="place"
            />
            <LTileLayer :url="url"/>
          </LMap>
        </card>
      </div>

      <div class="col-12 col-lg-4">
        <card title="Категории" class="mb-4">
          <b-form-checkbox
            v-for="category in categories"
            :id="'MapCategory-' + category.id"
            :key="category.id"
            v-model="selectedCategoryIds"
            :value="category.id"
          >
            {{ category.name }}
          </b-form-checkbox>
        </card>

        <MapPlaceSelector
          :is-active="addPlaceMode"
          @selectionStart="addPlaceMode = true"
          @selectionCancel="addPlaceMode = false"
        />
      </div>
    </div>

    <b-modal
      id="MapPlaceCreateModal"
      title="Выберите категории"
      @hidden="resetNewPlaceData"
    >
      <div class="form-group">
        <b-form-checkbox
          v-for="category in categories"
          :id="'NewPlaceCategory-' + category.id"
          :key="category.id"
          v-model="newPlaceCategories"
          :value="category.id"
        >
          {{ category.name }}
        </b-form-checkbox>
      </div>
      <div class="form-group">
        <label>Комментарий</label>
        <b-form-textarea
          id="textarea-state"
          v-model="newPlaceComment"
          placeholder="Добавьте комментарий, если необходимо"
          rows="3"
        ></b-form-textarea>
      </div>

      <template v-slot:modal-footer>
        <button
          class="btn btn-primary"
          :disabled="!newPlaceDataValid"
          @click="submitNewPlace"
        >
          <template v-if="newPlaceDataValid">
            Добавить
          </template>
          <template v-else-if="newPlaceCategories.length === 0">
            Выберите категории
          </template>
          <template v-else-if="!newPlaceCoordinates">
            Не выбрана координата
          </template>
        </button>
        <button
          class="btn btn-danger"
          @click="resetNewPlaceData"
        >
          Отмена
        </button>
      </template>
    </b-modal>
  </div>
</template>

<script>
import axios from 'axios'
import { debounce, intersection } from 'lodash-es'
import { mapGetters, mapActions } from 'vuex'
import { LMap, LTileLayer } from 'vue2-leaflet'
import MapPlaceSelector from '~/components/MapPlaceSelector'
import MapMarker from '~/components/MapMarker'

export default {
  name: 'Map',

  components: {
    LMap,
    LTileLayer,
    MapMarker,
    MapPlaceSelector,
  },

  data: () => ({
    url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
    selectedCategoryIds: [],
    addPlaceMode: false,
    newPlaceCoordinates: null,
    newPlaceCategories: [],
    newPlaceComment: null,
  }),

  computed: {
    selectedCategories () {
      return this.categories.filter(cat => this.selectedCategoryIds.includes(cat.id))
    },
    selectedPlaces () {
      return this.places.filter(place => {
        let placeCatIds = place.categories.map(cat => cat.id)
        return intersection(this.selectedCategoryIds, placeCatIds).length > 0
      })
    },
    newPlaceDataValid () {
      return (!!this.newPlaceCoordinates && this.newPlaceCategories.length > 0)
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
    mapClickHandler ({ latlng }) {
      if (this.addPlaceMode) {
        this.newPlaceCoordinates = latlng
        this.$bvModal.show('MapPlaceCreateModal')
      }
    },
    resetNewPlaceData () {
      this.addPlaceMode = false
      this.newPlaceCoordinates = null
      this.newPlaceCategories = []
      this.newPlaceComment = null
      this.$bvModal.hide('MapPlaceCreateModal')
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
    submitNewPlace: debounce.call(this, function () {
      if (!this.newPlaceDataValid) {
        return
      }
      this.sendPointToServer({
        lat: this.newPlaceCoordinates.lat,
        lng: this.newPlaceCoordinates.lng,
        categories: this.newPlaceCategories,
        comment: this.newPlaceComment,
      })
      this.$bvModal.hide('MapPlaceCreateModal')
    }, 100),
    async sendPointToServer ({ lat, lng, categories, comment }) {
      const { data: { data: place } } = await axios.post('/api/places', {
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
