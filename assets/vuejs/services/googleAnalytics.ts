import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { event } from 'vue-gtag'
import { getIdFromIri } from '@/vuejs/services/formatter'

/**
 * envoie un événement personnalisé à la couche de données (dataLayer) avec les données fournies
 * @param userId
 * @param channelName
 */
export const buildStandardGaData = (
  userId: any | null,
  channelName: string,
): Record<any, any> => {
  return {
    device: Screen.orientation > 1 ? 'phone' : 'desktop',
    navigateur: userBrowser(),
    user_id: getIdFromIri(userId),
    marketplace: channelName,
  }
}

export const sendGaEvent = (eventName: string, additionalData = null) => {
  const userStore = useUserStore()
  const channelStore = useChannelStore()

  let data = buildStandardGaData(
    userStore.user?.['@id'],
    channelStore.currentChannel.name,
  )
  data = additionalData ? { ...data, ...additionalData } : data
  event(eventName, data)
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
