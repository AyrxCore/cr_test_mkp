export interface SavedCart {
  id?: string
  accountId?: string
  name?: string
  createdAt?: Date
  updatedAt?: Date
  savedCartProducts?: Array<object>
}
