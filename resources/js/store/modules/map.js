import { supportsLocalStorage } from '~/helpers/helpers'
import axios from 'axios'

const supportsStorage = supportsLocalStorage()
let zoom = 5
let center = [55.677357, 37.787476]

if (supportsStorage && localStorage.getItem('map_zoom')) {
  zoom = Number(localStorage.getItem('map_zoom'))
}
if (supportsStorage && localStorage.getItem('map_center')) {
  center = JSON.parse(localStorage.getItem('map_center'))
}

export const state = () => ({
  zoom,
  center,
  bounds: [],
  places: [],
  categories: [],
})

export const getters = {
  zoom: state => state.zoom,
  center: state => state.center,
  bounds: state => state.bounds,
  places: state => state.places,
  categories: state => state.categories,
}

export const mutations = {
  SET_ZOOM (state, { zoom }) {
    state.zoom = zoom
  },
  SET_CENTER (state, { center }) {
    state.center = center
  },
  SET_BOUNDS (state, { bounds }) {
    state.bounds = bounds
  },
  SET_PLACES (state, { places }) {
    state.places = places
  },
  ADD_PLACE (state, { place }) {
    state.places.push(place)
  },
  SET_CATEGORIES (state, { categories }) {
    state.categories = categories
  },
}

export const actions = {
  setZoom ({ commit }, { zoom }) {
    commit('SET_ZOOM', { zoom })
    if (supportsStorage) {
      localStorage.setItem('map_zoom', zoom)
    }
  },
  setCenter ({ commit }, { center }) {
    commit('SET_CENTER', { center })
    if (supportsStorage) {
      localStorage.setItem('map_center', JSON.stringify(center))
    }
  },
  setBounds ({ commit }, { bounds }) {
    commit('SET_BOUNDS', { bounds })
  },
  setPlaces ({ commit }, { places }) {
    commit('SET_PLACES', { places })
  },
  addPlace ({ commit }, { place }) {
    commit('ADD_PLACE', { place })
  },
  setCategories ({ commit }, { categories }) {
    commit('SET_CATEGORIES', { categories })
  },
  async fetchCategories ({ commit }) {
    let { data: { data: categories } } = await axios.get('/api/categories')
    commit('SET_CATEGORIES', { categories })
  },
}
