<template>
  <div
    class="relative"
    :class="{ 'rounded-md': !isMobile }"
  >
    <slot name="title" />

    <div class="relative">
      <BaseMap
        :zoom="zoom"
        :center="userPositionCoords || center"
        :enable-zoom="enableZoom"
        class="w-full"
        @update:zoom="zoom = $event"
        @update:center="handleCenterUpdate"
        @map-ready="onMapReady"
      >
        <template #markers>
          <slot name="markers" />
          <LCircle
            v-if="isGeolocationActive && enableGeolocation"
            :lat-lng="userPositionCoords"
            :radius="circleRadius"
            :color="poiColor"
            fill
          />
        </template>

        <template #controls>
          <LControl v-if="enableControls" position="bottomright">
            <MapControls v-if="isGeolocationActive" @recenter="recenterMap" />
          </LControl>
        </template>
      </BaseMap>

      <div v-if="!isGeolocationActive && enableGeolocation" class="absolute inset-0">
        <GeolocationComponent
          :is-loading="isLoading"
          @geolocation-request="handleGeolocation"
        />
      </div>
    </div>

    <slot v-if="enableGeolocation" name="footer">
      <GeolocationStatusComponent
        :geolocation-active="isGeolocationActive"
        :error="displayedGeolocError"
        :is-loading="isLoading"
        @retry="handleGeolocation"
      />
    </slot>
  </div>
</template>

<script lang="ts" setup>
import { computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { LCircle, LControl } from '@vue-leaflet/vue-leaflet'
import type { Map as LeafletMap } from 'leaflet'

import { useGeolocation } from '@/vuejs/modules/products/composables/useGeolocation'
import { useMap } from '@/vuejs/modules/products/composables/useMap'

import BaseMap from '@/vuejs/modules/shared/map/BaseMap.vue'

import GeolocationComponent from '@/vuejs/modules/shared/map/GeolocationComponent.vue'
import GeolocationStatusComponent from '@/vuejs/modules/shared/map/GeolocationStatusComponent.vue'
import MapControls from '@/vuejs/modules/shared/map/MapControls.vue'

import {
  PARIS_COORDINATES,
  DEFAULT_ZOOM,
  MOBILE_ZOOM,
} from '@/vuejs/modules/products/utils/map-utils'

defineProps<{
  poiColor: string
  enableGeolocation?: boolean
  enableControls?: boolean
  enableZoom?: boolean
}>()

const {
  userPosition,
  isGeolocationActive,
  isLoading,
  displayedGeolocError,
  userPositionCoords,
  handleGeolocation,
  loadSavedLocation,
} = useGeolocation()

const {
  leafletMap,
  zoom,
  center,
  fixedTooltips,
  isMobile,
  checkIfMobile,
  recenterMap,
} = useMap()

const circleRadius = computed(() => {
  const baseRadius = 100
  const zoomFactor = Math.pow(2, 13 - zoom.value)
  return Math.min(baseRadius * zoomFactor, 5000)
})

const handleCenterUpdate = (newCenter) => {
  if (Array.isArray(newCenter)) center.value = newCenter
}

const onMapReady = (mapInstance: LeafletMap) => {
  try {
    leafletMap.value = mapInstance

    const initialCenter = Array.isArray(center.value)
      ? center.value
      : PARIS_COORDINATES
    mapInstance.setView(initialCenter, zoom.value)

    mapInstance.on('click', (e) => {
      if (!e.originalEvent) return

      const target = e.originalEvent.target
      if (!target) return

      const isMarkerClick = target.closest
        ? target.closest('.leaflet-marker-icon')
        : null
      const isTooltipClick = target.closest
        ? target.closest('.leaflet-tooltip')
        : null

      if (!isMarkerClick && !isTooltipClick) {
        fixedTooltips.value = []
      }
    })

    const savedLocation = loadSavedLocation()
    if (savedLocation) {
      requestAnimationFrame(() => {
        mapInstance.setView(savedLocation, zoom.value)
        center.value = savedLocation
      })
    }

    mapInstance.options.tap = true
    mapInstance.options.touchZoom = true
    mapInstance.options.bounceAtZoomLimits = false
  } catch (error) {
    console.error("Erreur lors de l'initialisation de la carte:", error)
  }
}

onMounted(async () => {
  checkIfMobile()
  window.addEventListener('resize', checkIfMobile)

  const savedLocation = loadSavedLocation()
  if (savedLocation) {
    center.value = savedLocation
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', checkIfMobile)
})

watch(
  () => userPosition.value,
  (newPosition) => {
    if (newPosition) {
      const [lat, lng] = Array.isArray(newPosition)
        ? newPosition
        : [newPosition.lat, newPosition.lng]
      if (leafletMap.value) {
        const newZoom = isMobile.value ? MOBILE_ZOOM : DEFAULT_ZOOM
        leafletMap.value.setView([lat, lng], newZoom)
        zoom.value = newZoom
        center.value = [lat, lng]
      }
    }
  },
)
</script>
