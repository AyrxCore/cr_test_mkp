<template>
  <div
    class="relative w-full overflow-hidden rounded-lg"
    :class="heightClass || 'h-64 md:h-96 lg:h-[500px]'"
  >
    <div
      v-if="isLoading"
      class="absolute inset-0 z-50 flex items-center justify-center bg-white bg-opacity-75"
    >
      <div
        class="h-8 w-8 animate-spin rounded-full border-4 border-primary border-t-transparent"
      />
    </div>

    <LMap
      :zoom="zoom"
      :center="center"
      :use-global-leaflet="true"
      class="z-0 h-full w-full"
      :options="getMapOptions(enableZoom)"
      @update:zoom="$emit('update:zoom', $event)"
      @update:center="$emit('update:center', $event)"
      @ready="onMapReady"
    >
      <LTileLayer
        :url="TILE_URL"
        :attribution="ATTRIBUTION"
        layer-type="base"
      />
      <slot name="markers" />
      <slot name="controls" />
    </LMap>
  </div>
</template>

<script lang="ts" setup>
import { LMap, LTileLayer } from '@vue-leaflet/vue-leaflet'
import * as L from 'leaflet'
import type { Map as LeafletMap } from 'leaflet'
import {
  getMapOptions,
  getZoomControl,
  TILE_URL,
  ATTRIBUTION,
} from '@/vuejs/modules/products/utils/map-utils'
import { ref } from 'vue'

const props = defineProps({
  zoom: {
    type: Number,
    required: true,
  },
  center: {
    type: Array,
    required: true,
    validator: (value: unknown): value is [number, number] =>
      Array.isArray(value) &&
      value.length === 2 &&
      typeof value[0] === 'number' &&
      typeof value[1] === 'number',
  },
  enableZoom: {
    type: Boolean,
    default: true,
  },
  heightClass: {
    type: String,
    required: false,
    default: null,
  },
})

const emit = defineEmits<{
  'update:zoom': [value: number]
  'update:center': [value: [number, number]]
  'map-ready': [map: LeafletMap]
}>()

const isLoading = ref<boolean>(true)

const onMapReady = (mapInstance: LeafletMap) => {
  try {
    const zoomControl = getZoomControl(props.enableZoom)
    if (zoomControl) {
      L.control.zoom(zoomControl).addTo(mapInstance)
    }

    mapInstance.options.tap = true
    mapInstance.options.touchZoom = true
    mapInstance.options.bounceAtZoomLimits = false

    emit('map-ready', mapInstance)

    isLoading.value = false
  } catch (error) {
    console.error("Erreur lors de l'initialisation de la carte:", error)
    isLoading.value = false
  }
}
</script>
