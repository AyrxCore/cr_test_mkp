/**
 * envoie un événement personnalisé à la couche de données (dataLayer) avec les données fournies
 * @param {String} eventName - Le nom de l'événement à pousser.
 * @param {Object} eventData - Les données incluses dans l'événémént.
 */
export function gtmMixinPushEvent(eventName, eventData = {}) {
  if (typeof window !== 'undefined' && window.dataLayer) {
    window.dataLayer.push({
      event: eventName,
      ...eventData
    })
  }
}
