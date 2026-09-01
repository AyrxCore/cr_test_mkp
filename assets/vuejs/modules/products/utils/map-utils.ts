import type { LatLngTuple } from 'leaflet'

export const PARIS_COORDINATES = [48.8566, 2.3522] as LatLngTuple
export const DEFAULT_ZOOM = 12
export const MOBILE_ZOOM = 11

const TILE_BASE_URL =
  'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'

// La clé est injectée au runtime par Twig (window.__CARTO_API_KEY__), pas au build Vite,
// pour que la même image Docker serve staging/preprod/prod.
export function getTileUrl(): string {
  const key = window.__CARTO_API_KEY__

  return key ? `${TILE_BASE_URL}?key=${encodeURIComponent(key)}` : TILE_BASE_URL
}

export const ATTRIBUTION =
  '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'

export const getMapOptions = (enableZoom = true) => ({
  zoomAnimation: false,
  markerZoomAnimation: false,
  fadeAnimation: false,
  zoomControl: false,
  scrollWheelZoom: enableZoom,
  doubleClickZoom: enableZoom,
  dragging: true,
  easeLinearity: 0,
  preferCanvas: true,
})

export const getZoomControl = (enableZoom = true) => {
  if (!enableZoom) return null

  return {
    position: 'bottomright',
    zoomInTitle: 'Zoomer',
    zoomOutTitle: 'Dézoomer',
  }
}

export function getLatLng(
  latitude: string,
  longitude: string,
): [number, number] {
  if (!latitude || !longitude) {
    return PARIS_COORDINATES
  }

  return [parseFloat(latitude), parseFloat(longitude)]
}
