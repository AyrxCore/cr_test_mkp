export const MENTIONS_LEGALES_PAGE_ID = 107
export const CGU_PAGE_ID = 108
export const POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID = 109

export const ADDRESS_BILLING = 'billing'
export const ADDRESS_SHIPPING = 'shipping'

export const ORDER_STATUS = {
  new: {
    name: 'En attente',
    color: 'bg-primary',
  },
  pending: {
    name: 'En attente',
    color: 'bg-primary',
  },
  confirmed: {
    name: 'Confirmée',
    color: 'bg-green-500',
  },
  edited: {
    name: 'Modification transmise',
    color: 'bg-primary',
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
    color: 'bg-primary',
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
    color: 'bg-primary',
  },
  cancelled: {
    name: 'Annulé',
    color: 'bg-red-600',
  },
}

/**
 * Those parameters are usefull to get differents products to display on the home page as there label
 */
export const HOME_TOP_VENTE_PROPERTY = {
  perPage: 5,
  properties: {
    property_id: 217,
    value: 5369,
  },
}

export const HOME_SELECTION_PROPERTY = {
  perPage: 5,
  properties: {
    property_id: 217,
    value: 5368,
  },
}

export const HOME_ACCORD_CADRE_PROPERTY = {
  perPage: 5,
  properties: {
    property_id: 217,
    value: 5367,
  },
}

export const OPTIONAL_FRONT_BLOCKS = {
  BANNER_FLASH_HOMEPAGE: 'BANNER_FLASH_HOMEPAGE',
  BANNER_SLIDER_HOMEPAGE: 'BANNER_SLIDER_HOMEPAGE',
  RSE_HOMEPAGE: 'RSE_HOMEPAGE',
  EXPERT_CONTENT_HOMEPAGE: 'EXPERT_CONTENT_HOMEPAGE',
  SUPPLIER_PARTNERS_HOMEPAGE: 'SUPPLIER_PARTNERS_HOMEPAGE',
  FAVORITES: 'FAVORITES',
  PROMOTIONAL_FAT: 'PROMOTIONAL_FAT',
  SAVED_CARTS: 'SAVED_CARTS',
}

/**
 * End
 */
