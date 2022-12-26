interface BuyerDefaultAddress {
  id: number
  street: string
  postcode: string
  city: string
  province: null | string
}

interface AccountBuyer {
  id: number
  name: string
  avatar: string
  phone: string
  default_address: BuyerDefaultAddress
  number: number
  email: string
  website: string
}

export interface SubAccount {
  id: number
  email: string | null
  lastname: string | null
  firstname: string | null
  shipping_address: number | null
  billing_address: number | null
}

export interface Account {
  id: string
  lastConnexion: Date
  buyer: AccountBuyer
  subaccount: SubAccount
}

export interface DefaultBillingAddressToUpdate {
  billingAddressId: number
  id: number
}

export interface DefaultShippingAddressToUpdate {
  shippingAddressId: number
  id: number
}
