interface CommonAddress {
  fullName: string
  address: string
  zipcode: string
  city: string
  country: string
  phone: string
  shipping: boolean
  billing: boolean
}

export interface Address extends CommonAddress {
  id: string | null
  externalId: string | null
}

export type AddressToCreate = CommonAddress


export interface AddressStoreState {
  addresses: Address[]
  currentAddress: Address
  isLoading: boolean
}
