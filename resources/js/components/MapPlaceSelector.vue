<template>
  <card title="Добавить новое место">

    <div v-if="isActive">
      <p class="text-center mb-3">Выберите точку на карте</p>
      <button
        class="btn btn-block btn-danger"
        @click="deactivateSelector"
      >
        Отмена
      </button>
    </div>
    <button
      v-else
      class="btn btn-block btn-primary"
      @click="activateSelector"
    >
      Нажмите, чтобы добавить
    </button>
  </card>
</template>

<script>
export default {
  name: 'MapPlaceSelector',

  props: {
    isActive: Boolean,
  },

  methods: {
    activateSelector () {
      this.$emit('selectionStart')
    },
    deactivateSelector () {
      this.$emit('selectionCancel')
    },
  },
}
</script>
