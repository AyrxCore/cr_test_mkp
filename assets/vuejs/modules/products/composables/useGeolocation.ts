import { ref, computed } from 'vue'
import { useUserStore } from '@/vuejs/stores/user'
import type { LatLngTuple } from 'leaflet'
import { PARIS_COORDINATES } from '../utils/map-utils'

export function useGeolocation() {
  const userStore = useUserStore()
  const userPosition = ref<LatLngTuple>([])
  const isGeolocationActive = ref<boolean>(userStore.isGeolocationAvailable)
  const isLoading = ref<boolean>(false)
  const geolocError = ref<string>('')
  const isFirstAttempt = ref<boolean>(true)

  const displayedGeolocError = computed(
    () =>
      geolocError.value ||
      (userStore.hasGeolocationError
        ? userStore.getGeolocationErrorMessage()
        : ''),
  )

  const userPositionCoords = computed((): LatLngTuple => {
    if (userPosition.value && userPosition.value.length === 2) {
      return userPosition.value
    }
    return PARIS_COORDINATES
  })

  const handleGeolocation = () => {
    isLoading.value = true
    geolocError.value = ''

    if (!navigator.geolocation) {
      handleGeolocationFailure(
        "La géolocalisation n'est pas supportée par votre navigateur",
      )
      return
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords

        userStore.saveUserLocation({ lat: latitude, lng: longitude })
        userStore.setGeolocationError('')

        userPosition.value = [latitude, longitude]
        isGeolocationActive.value = true
        geolocError.value = ''
        isLoading.value = false
        isFirstAttempt.value = false

        return { latitude, longitude }
      },
      (error) => {
        let errorMessage = "La géolocalisation n'a pas pu être effectuée"

        if (
          window.location.protocol === 'http:' &&
          error.code === error.PERMISSION_DENIED
        ) {
          errorMessage =
            "La géolocalisation n'est pas disponible en HTTP. Essayez en HTTPS ou vérifiez les paramètres de votre navigateur"
        } else {
          switch (error.code) {
            case error.PERMISSION_DENIED:
              errorMessage =
                "Vous avez refusé l'accès à votre position dans votre navigateur"
              break
            case error.POSITION_UNAVAILABLE:
              errorMessage = "Votre position n'est pas disponible actuellement"
              break
            case error.TIMEOUT:
              errorMessage = 'La demande de géolocalisation a expiré'
              break
            default:
              if (error.message) {
                errorMessage = `Erreur: ${error.message}`
              }
          }
        }

        handleGeolocationFailure(errorMessage)
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
      },
    )
  }

  const handleGeolocationFailure = (errorMessage: string) => {
    if (!isFirstAttempt.value && userPosition.value.length === 2) {
      userStore.setGeolocationError(errorMessage)
      geolocError.value = errorMessage
      isLoading.value = false

      return userPosition.value
    }

    const parisLocation = {
      lat: PARIS_COORDINATES[0],
      lng: PARIS_COORDINATES[1],
    }

    userStore.saveUserLocation(parisLocation)
    userStore.setGeolocationError(errorMessage)

    userPosition.value = PARIS_COORDINATES
    isGeolocationActive.value = true
    geolocError.value = errorMessage
    isLoading.value = false
    isFirstAttempt.value = false

    return PARIS_COORDINATES
  }

  const loadSavedLocation = () => {
    if (userStore.hasGeolocationError) {
      geolocError.value = userStore.getGeolocationErrorMessage()
    }

    userStore.loadUserLocation()
    isGeolocationActive.value = userStore.isGeolocationAvailable

    if (userStore.userLocation) {
      const { lat, lng } = userStore.userLocation
      userPosition.value = [lat, lng]
      isGeolocationActive.value = true
      isFirstAttempt.value = false
      return [lat, lng]
    }

    return null
  }

  return {
    userPosition,
    isGeolocationActive,
    isLoading,
    geolocError,
    displayedGeolocError,
    userPositionCoords,
    handleGeolocation,
    handleGeolocationFailure,
    loadSavedLocation,
  }
}
