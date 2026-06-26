import {
  Address,
  AddressToCreate,
} from '@/vuejs/types/Address'

export function setAddressForCreate(address: Address): AddressToCreate {
  return {
    fullName: address.fullName,
    address: address.address,
    zipcode: address.zipcode,
    city: address.city,
    country: address.country,
    phone: address.phone ? address.phone : '',
    shipping: address.shipping,
    billing: address.billing,
  }
}

export function setAddressForUpdate(address: Address): Address {
  return {
    id: address.id,
    externalId: address.externalId,
    fullName: address.fullName,
    address: address.address,
    zipcode: address.zipcode,
    city: address.city,
    country: address.country,
    phone: address.phone ? address.phone : '',
    shipping: address.shipping,
    billing: address.billing,
  }
}
