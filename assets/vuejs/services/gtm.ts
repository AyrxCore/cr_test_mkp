import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'

/**
 * envoie un événement personnalisé à la couche de données (dataLayer) avec les données fournies
 * @param {String} eventName - Le nom de l'événement à pousser.
 * @param {Object} eventData - Les données incluses dans l'événémént.
 */
export const gtmMixinPushEvent = (eventName: string, eventData = {}) => {
  if (typeof window !== 'undefined' && window.dataLayer) {
    window.dataLayer.push({
      event: eventName,
      ...eventData,
    })
  }
}

export const buildStandardGtmData = (userId, channelName): Record<any, any> => {
  return {
    device: Screen.orientation > 1 ? 'phone' : 'desktop',
    screen_size: window.innerWidth + 'x' + window.innerHeight,
    navigateur: userBrowser(),
    page_url: window.location.href,
    user_id: userId,
    marketplace: channelName,
  }
}

export const sendGtmEvent = (eventName: string, additionalData = null) => {
  const userStore = useUserStore()
  const channelStore = useChannelStore()

  let data = buildStandardGtmData(
    userStore.user['@id'],
    channelStore.currentChannel.name,
  )
  data = additionalData ? { ...data, ...additionalData } : data
  gtmMixinPushEvent(eventName, data)
}

const userBrowser = () => {
  const navigator = window.navigator
  if (
    (navigator.userAgent.indexOf('Opera') ||
      navigator.userAgent.indexOf('OPR')) !== -1
  ) {
    return 'opera'
  } else if (navigator.userAgent.indexOf('Edg') !== -1) {
    return 'edge'
  } else if (navigator.userAgent.indexOf('Chrome') !== -1) {
    return 'chrome'
  } else if (navigator.userAgent.indexOf('Safari') !== -1) {
    return 'safari'
  } else if (navigator.userAgent.indexOf('Firefox') !== -1) {
    return 'firefox'
  } else if (
    navigator.userAgent.indexOf('MSIE') !== -1 ||
    !!document.documentMode === true
  ) {
    return 'IE'
  } else {
    return null
  }
}
