<template>
  <div class="relative" :class="{ 'rounded-md': !isMobile }">
    <slot name="title" />

    <AddressSearchComponent
      v-if="isGeolocationActive"
      :class="[isMobile ? 'mb-4' : 'absolute left-4 top-4 z-[15] md:block']"
    />

    <div class="relative">
      <BaseMap
        :zoom="zoom"
        :center="userPositionCoords || center"
        :enable-zoom="enableZoom"
        :height-class="heightClass"
        class="w-full"
        @update:zoom="zoom = $event"
        @update:center="handleCenterUpdate"
        @map-ready="onMapReady"
      >
        <template #markers>
          <slot name="markers" />
          <template v-if="isGeolocationActive && enableGeolocation">
            <LCircle
              :lat-lng="userPositionCoords"
              :radius="circleRadius * 1.6"
              color="#b3c8e3"
              :fill-color="'#b3c8e3'"
              :fill-opacity="0.5"
              :weight="0"
              fill
            />
            <LCircle
              :lat-lng="userPositionCoords"
              :radius="circleRadius * 0.9"
              color="white"
              :fill-color="'#4285f4'"
              :fill-opacity="1"
              :weight="1.5"
              fill
            />
          </template>
          <LMarker
            v-if="selectedAddress"
            :icon="createMarkerIcon('text-red-500')"
            :lat-lng="[
              parseFloat(selectedAddress.lat),
              parseFloat(selectedAddress.lon),
            ]"
          />
        </template>
        <template #controls>
          <LControl
            v-if="enableControls"
            position="bottomright"
            class="map-geolocation-control"
          >
            <MapControls v-if="isGeolocationActive" @recenter="recenterMap" />
          </LControl>
        </template>
      </BaseMap>

      <div
        v-if="!isGeolocationActive && enableGeolocation"
        class="absolute inset-0"
      >
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
import { computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { LMarker, LCircle, LControl } from '@vue-leaflet/vue-leaflet'
import type { Map as LeafletMap } from 'leaflet'

import { useGeolocation } from '@/vuejs/modules/products/composables/useGeolocation'
import {
  leafletMap,
  selectedAddress,
  useMap,
} from '@/vuejs/modules/products/composables/useMap'

import BaseMap from '@/vuejs/modules/shared/map/BaseMap.vue'

import AddressSearchComponent from '@/vuejs/modules/shared/map/AddressSearchComponent.vue'
import GeolocationComponent from '@/vuejs/modules/shared/map/GeolocationComponent.vue'
import GeolocationStatusComponent from '@/vuejs/modules/shared/map/GeolocationStatusComponent.vue'
import MapControls from '@/vuejs/modules/shared/map/MapControls.vue'

import {
  PARIS_COORDINATES,
  DEFAULT_ZOOM,
  MOBILE_ZOOM,
} from '@/vuejs/modules/products/utils/map-utils'

defineProps<{
  enableGeolocation?: boolean
  enableControls?: boolean
  enableZoom?: boolean
  heightClass?: string
}>()

const emit = defineEmits<{
  'map-ready': [map: LeafletMap]
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
  zoom,
  center,
  fixedTooltips,
  isMobile,
  checkIfMobile,
  recenterMap,
  createMarkerIcon,
  isMapValid,
  cleanupMapReference,
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
      nextTick(() => {
        try {
          if (isMapValid(mapInstance)) {
            mapInstance.setView(savedLocation, zoom.value)
            center.value = savedLocation
          }
        } catch (error) {
          console.warn(
            'Erreur lors du chargement de la position sauvegardée:',
            error,
          )
          // Fallback: mettre à jour seulement les valeurs réactives
          center.value = savedLocation
        }
      })
    }

    mapInstance.options.tap = true
    mapInstance.options.touchZoom = true
    mapInstance.options.bounceAtZoomLimits = false

    emit('map-ready', mapInstance)
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

  cleanupMapReference()
})

watch(
  () => userPosition.value,
  (newPosition) => {
    if (newPosition && leafletMap.value) {
      const [lat, lng] = Array.isArray(newPosition)
        ? newPosition
        : [newPosition.lat, newPosition.lng]

      try {
        const newZoom = isMobile.value ? MOBILE_ZOOM : DEFAULT_ZOOM
        leafletMap.value.setView([lat, lng], newZoom)
        zoom.value = newZoom
        center.value = [lat, lng]
      } catch (error) {
        console.warn('Erreur lors de la mise à jour de la carte:', error)
        const newZoom = isMobile.value ? MOBILE_ZOOM : DEFAULT_ZOOM
        zoom.value = newZoom
        center.value = [lat, lng]
      }
    }
  },
)
</script>
