import { ref, h, render } from 'vue'
import * as L from 'leaflet'
import type { Map as LeafletMap, LatLngTuple } from 'leaflet'

import MarkerIconComponent from '@/vuejs/modules/shared/icon/MarkerIconComponent.vue'

import {
  PARIS_COORDINATES,
  DEFAULT_ZOOM,
  MOBILE_ZOOM,
} from '@/vuejs/modules/products/utils/map-utils'
import type { AddressSearchResult } from '@/vuejs/services/searchAddressapi'

export const leafletMap = ref<LeafletMap | null>(null)
export const selectedAddress = ref<AddressSearchResult | null>(null)

export function getDisplayedName(result: AddressSearchResult): string {
  if (result.address) {
    const {
      house_number,
      road,
      postcode,
      village,
      city,
      town,
      state,
      region,
      country,
    } = result.address
    return [
      house_number ? `${house_number} ${road || ''}`.trim() : road,
      postcode ? postcode : '',
      village || city || town || state || region,
      country,
    ]
      .filter(Boolean)
      .join(', ')
  }
  return result.display_name || ''
}

export function useMap() {
  const zoom = ref<number>(DEFAULT_ZOOM)
  const center = ref<LatLngTuple>(PARIS_COORDINATES)
  const fixedTooltips = ref<string[]>([])
  const isMobile = ref<boolean>(false)

  const createMarkerIcon = (colorClass: string) => {
    const iconSize = 40
    const container = document.createElement('div')
    render(h(MarkerIconComponent), container)

    return L.divIcon({
      html: container.innerHTML,
      className: `custom-marker-container flex items-center ${colorClass}`,
      iconSize: [iconSize, iconSize],
      iconAnchor: [iconSize / 2, iconSize],
      interactive: true,
      bubblingMouseEvents: false,
    })
  }

  /**
   * Get tooltip options based on store ID and active state
   * @param storeId The store ID to check
   * @returns Tooltip options object
   */
  const getTooltipOptions = (storeId: string) => {
    return {
      permanent: fixedTooltips.value.includes(storeId),
      direction: 'top',
      className: `map-tooltip-base${isMobile.value ? ' map-tooltip-mobile' : ''}`,
      offset: [0, -38],
      interactive: true,
    }
  }

  /**
   * Check if the device is mobile and update zoom accordingly
   */
  const checkIfMobile = () => {
    isMobile.value = window.innerWidth < 768
    if (leafletMap.value) {
      zoom.value = isMobile.value ? MOBILE_ZOOM : DEFAULT_ZOOM
    }
  }

  /**
   * Recenter the map on the current center position
   */
  const recenterMap = () => {
    if (leafletMap.value && center.value) {
      const newZoom = isMobile.value ? MOBILE_ZOOM : DEFAULT_ZOOM
      leafletMap.value.setView(center.value, newZoom)
      zoom.value = newZoom
    }
  }

  return {
    leafletMap,
    zoom,
    center,
    fixedTooltips,
    isMobile,
    createMarkerIcon,
    getTooltipOptions,
    checkIfMobile,
    recenterMap,
  }
}
