export interface FavoriteProduct {
  id?: string
  upplerProductId?: number
  upplerVariantId?: number
  upplerProductName?: string
}

export interface Favorite {
  id?: string
  accountId?: string
  name?: string
  public?: boolean
  createdAt?: Date
  updatedAt?: Date
  favoriteProducts?: Array<FavoriteProduct>
}
