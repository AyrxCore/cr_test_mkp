export interface Seller {
  id: number
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
}

export interface SellerStoreState {
  sellers: Seller[]
  promotions: {
    [key: number]: SellerPromotion[]
  }
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
