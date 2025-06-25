<template>
  <LControl position="bottomright" class="map-geolocation-control">
    <button
      class="mr-[2px] flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-100 focus:outline-none"
      title="Recentrer sur ma position"
      @click="$emit('recenter')"
    >
      <LocationIconComponent class="!m-0" />
    </button>
  </LControl>
</template>

<script lang="ts" setup>
import { LControl } from '@vue-leaflet/vue-leaflet'
import LocationIconComponent from '@/vuejs/modules/shared/icon/LocationIconComponent.vue'
defineEmits(['recenter'])
</script>
