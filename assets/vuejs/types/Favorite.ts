export interface FavoriteProduct {
  id?: string
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
