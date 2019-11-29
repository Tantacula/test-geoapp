<template>
  <LMarker
    :key="place.id"
    :lat-lng="[place.point.lat, place.point.lng]"
  >
    <LPopup>
      <div class="mb-3">
        <strong>Добавлено:</strong> {{ place.created_at_readable }}
        <br>
        <span v-for="cat in place.categories"
              :key="cat.id"
              class="badge badge-info mr-1">
          {{ cat.name }}
        </span>
      </div>
      <div v-if="place.comment">
        {{ place.comment }}
      </div>
      <div v-else>
        Нет комментария к данному месту
      </div>
    </LPopup>
  </LMarker>
</template>

<script>
import { LMarker, LPopup } from 'vue2-leaflet'
import { hasProperties } from '~/helpers/helpers'

export default {
  name: 'MapMarker',

  components: {
    LMarker,
    LPopup,
  },

  props: {
    place: {
      type: Object,
      required: true,
      validator (place) {
        return hasProperties(place, ['id', 'point', 'created_at_readable', 'categories'])
      },
    },
  },
}
</script>
