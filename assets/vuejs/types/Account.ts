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
  phone: string | null
}

export interface Adherent {
  reducceCode: string | null
}

export interface Account {
  id: string
  lastConnexion: Date
  upplerUserId: number
  buyer: AccountBuyer
  adherent: Adherent
  subaccount: SubAccount
  editingSubAccount: SubAccount
}

export interface DefaultBillingAddressToUpdate {
  id: number
  accountId: string
  billingAddressId: number
}

export interface DefaultShippingAddressToUpdate {
  id: number
  accountId: string
  shippingAddressId: number
}

interface AccountToUpdate {
  email: string
  id: number
  accountId: string
  lastName: string
  firstName: string
  phone: string
}

export type AccountEmail = Omit<
  AccountToUpdate,
  'lastName' | 'firstName' | 'phone'
>

export type AccountDetails = Omit<AccountToUpdate, 'email'>
