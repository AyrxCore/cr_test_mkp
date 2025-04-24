// useMap.ts - Improved version
import { ref, h, render } from 'vue'
import * as L from 'leaflet'
import type { Map as LeafletMap, LatLngTuple, LeafletMouseEvent } from 'leaflet'
import MarkerIconComponent from '@/vuejs/modules/shared/icon/MarkerIconComponent.vue'
import {
  PARIS_COORDINATES,
  DEFAULT_ZOOM,
  MOBILE_ZOOM,
} from '@/vuejs/modules/products/utils/map-utils'

export function useMap() {
  const leafletMap = ref<LeafletMap | null>(null)
  const zoom = ref<number>(DEFAULT_ZOOM)
  const center = ref<LatLngTuple>(PARIS_COORDINATES)
  const fixedTooltips = ref<string[]>([])
  const isMobile = ref<boolean>(false)

  const createMarkerIcon = () => {
    const iconSize = 40
    const container = document.createElement('div')
    const vnode = h('div', { class: 'flex items-center' }, [
      h(MarkerIconComponent),
    ])
    render(vnode, container)

    return L.divIcon({
      html: container.innerHTML,
      className: 'custom-marker-container flex items-center',
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
   * Handle marker click event - toggle tooltip visibility
   * @param event Leaflet mouse event
   * @param storeId The store ID that was clicked
   */
  const handleMarkerClick = (event: LeafletMouseEvent, storeId: string) => {
    const index = fixedTooltips.value.indexOf(storeId)

    if (index === -1) {
      fixedTooltips.value = [storeId]
    } else {
      fixedTooltips.value.splice(index, 1)
    }
  }

  /**
   * Close a specific tooltip
   * @param storeId The store ID whose tooltip should be closed
   */
  const closeTooltip = (storeId: string) => {
    const index = fixedTooltips.value.indexOf(storeId)
    if (index !== -1) {
      fixedTooltips.value.splice(index, 1)
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
    handleMarkerClick,
    closeTooltip,
    checkIfMobile,
    recenterMap,
  }
}
