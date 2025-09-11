<template>
  <LControl class="map-geolocation-control" position="bottomright">
    <button
      class="mr-[2px] flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-100 focus:outline-none"
      title="Recentrer sur ma position"
      @click="recenter"
    >
      <LocationIconComponent class="!m-0" />
    </button>
  </LControl>
</template>

<script lang="ts" setup>
import { LControl } from '@vue-leaflet/vue-leaflet'

import router from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import LocationIconComponent from '@/vuejs/modules/shared/icon/LocationIconComponent.vue'

const emit = defineEmits(['recenter'])

const recenter = () => {
  emit('recenter')
  sendGtmEvent('geolocate_me_click', {
    origin_url: router.currentRoute.value.fullPath,
  })
}
</script>
