interface CommonAddress {
  type: string
  name: string
  company: string
  companyId: number
  street: string
  postcode: string
  city: string
  country: string
  phone: string
}

export interface Address extends CommonAddress {
  id: string | number | null
  last_name: string
  first_name: string
}

export interface AddressToCreate extends CommonAddress {
  lastName: string
  firstName: string
}

export interface AddressToUpdate extends CommonAddress {
  id: string | number | null
  lastName: string
  firstName: string
}
