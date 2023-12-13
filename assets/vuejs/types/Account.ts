import { CollectionResponse } from '@/vuejs/types/HttpClient'
import { ExternalApiDataEntity } from '@/vuejs/types/ExternalApiDataEntity'
import { User } from '@/vuejs/types/User'

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
  logo: string | null
}

export interface Account extends ExternalApiDataEntity {
  id: string
  lastConnexion: Date
  upplerUserId: number
  buyer: AccountBuyer
  adherent: Adherent
  subaccount: SubAccount
  editingSubAccount: SubAccount
  acceptCGU: boolean
  user: User
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

export interface AccountCollectionResponse extends CollectionResponse {
  'hydra:member': Account[]
}

export type AccountEmail = Omit<
  AccountToUpdate,
  'lastName' | 'firstName' | 'phone'
>

export type AccountDetails = Omit<AccountToUpdate, 'email'>
