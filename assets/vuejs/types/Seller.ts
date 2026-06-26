import { Category } from '@/vuejs/types/Product/Category'

export interface AccordLogo {
  logo: string
  name: string
  id: string
}

export interface StoreData {
  id: string
  name: string
  address: string
  phone: string
  latitude: string
  longitude: string
  logo?: string
  partnerName?: string
  accordLogos?: AccordLogo[]
  djustId?: string
}

export interface StoreLightData {
  id: string
  latitude: string
  longitude: string
}
export interface Partner {
  id: number
  name: string
  partnerStores: StoreData[]
  upplerId?: number
}

export interface Seller {
  id: string
  externalId: string
  name: string
  corporateName?: string
  description?: string
  avatar?: string
  tos?: {
    id: number
    content: string
    state: string
    url: string
    uuid: string
    created_at: string
    updated_at: string
  }
  supplierDeliveryInfo?: string
  address?: string
}

export interface SellerFranco {
  [key: string]: number
}
export interface SellerShippingCost {
  [key: string]: number
}

export interface SellerPromotion {
  id: number
  name: {
    fr: string
    default: string
  }
  conditions: [
    {
      id: number
      apply_type: string
      apply_value: number
    },
  ]
  state: string
  order_eligibility: {
    id: number
    operator: string
    amount: number
    amount_type: string
    amount_operator: string
  }
}

export interface SellerStoreState {
  allSellers: Seller[]
  sellersByParams: Seller[]
  promotions: {
    [key: number]: SellerPromotion[]
  }
}

export interface MapApiResponse {
  stores: StoreData[]
  categories: Category[]
}

export interface MapApiLightResponse {
  stores: StoreLightData[]
  categories: Category[]
}
