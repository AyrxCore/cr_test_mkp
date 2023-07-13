export interface SavedCartProduct {
  id?: string
  upplerProductId?: number
  upplerVariantId?: number
  upplerProductName?: string
  quantity?: number
}

export interface SavedCart {
  id?: string
  accountId?: string
  name?: string
  createdAt?: Date
  updatedAt?: Date
  savedCartProducts?: Array<object>
}
