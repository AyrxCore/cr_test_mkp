export interface Seller {
  id: number
  name: string
  corporateName?: string
  description?: string
  avatar?: string
}

export interface SellerStoreState {
  sellers: Seller[]
}
