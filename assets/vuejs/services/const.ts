export const MAIL_ANIMATION = 'marketplace@qantis.co'
export const PHONE_ANIMATION = '04 37 65 06 21'
export const MENTIONS_LEGALES_PAGE_ID = 107
export const CGU_PAGE_ID = 108
export const POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID = 109

export const ORDER_STATUS = {
  new: {
    name: 'En attente',
    color: 'bg-qantis',
  },
  pending: {
    name: 'En attente',
    color: 'bg-qantis',
  },
  confirmed: {
    name: 'Confirmée',
    color: 'bg-green-500',
  },
  edited: {
    name: 'Modification transmise',
    color: 'bg-qantis',
  },
  refused: {
    name: 'Refusée',
    color: 'bg-red-600',
  },
  expired: {
    name: 'Expirée',
    color: 'bg-red-600',
  },
  canceled: {
    name: 'Annulée',
    color: 'bg-red-600',
  },
}

export const SHIPPING_STATUS = {
  pending: {
    name: 'En attente',
    color: 'bg-qantis',
  },
  preparation: {
    name: 'En préparation',
    color: 'bg-green-500',
  },
  ready: {
    name: 'Prêt à envoyer',
    color: 'bg-green-500',
  },
  partially_shipped: {
    name: 'Partiellement expédié',
    color: 'bg-green-500',
  },
  shipped: {
    name: 'Expédié',
    color: 'bg-green-500',
  },
  delivered: {
    name: 'Livré',
    color: 'bg-green-500',
  },
  returned: {
    name: 'Retourné',
    color: 'bg-qantis',
  },
  cancelled: {
    name: 'Annulé',
    color: 'bg-red-600',
  },
}
