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
}
