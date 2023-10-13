import {
  Address,
  AddressToCreate,
  AddressToUpdate,
} from '@/vuejs/types/Address'

export function setAddressForCreate(address: Address): AddressToCreate {
  return {
    name: address.name,
    companyId: address.companyId,
    company: address.company,
    type: address.type,
    street: address.street,
    postcode: address.postcode,
    city: address.city,
    country: address.country,
    lastName: address.lastName ? address.lastName : '',
    firstName: address.firstName ? address.firstName : '',
    phone: address.phone ? address.phone : '',
  }
}

export function setAddressForUpdate(address: Address): AddressToUpdate {
  return {
    id: address.id,
    name: address.name,
    companyId: address.companyId,
    company: address.company,
    type: address.type,
    street: address.street,
    postcode: address.postcode,
    city: address.city,
    country: address.country,
    lastName: address.lastName ? address.lastName : '',
    firstName: address.firstName ? address.firstName : '',
    phone: address.phone ? address.phone : '',
  }
}
