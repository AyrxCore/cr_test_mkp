export const CGU_PAGE_ID = 108

export const ADDRESS_BILLING = 'BILLING'
export const ADDRESS_SHIPPING = 'SHIPPING'

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

export const CART_LINE_ACTIONS = {
  ADD_QUANTITY: 'ADD_QUANTITY',
  REPLACE_QUANTITY: 'REPLACE_QUANTITY',
}

export const PRODUCT_FDP_PREFIX = 'PRODUCT_FDP_'

export const MAP_BLOCK_ID = 'mapBlock'

export const ACCORD_CADRE_TYPE = {
  STANDARD: 'STANDARD',
  BONUUS: 'BONUUS',
  DIRECT: 'DIRECT',
}
