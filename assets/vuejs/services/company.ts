import {
  Address,
  AddressToCreate,
  AddressToUpdate,
} from '@/vuejs/types/Address'
import { v4 as uuidv4 } from 'uuid'
export function getEmptyAddress(companyId: number): Address {
  return {
    id: uuidv4(),
    name: '',
    companyId,
    company: '',
    type: '',
    street: '',
    postcode: '',
    city: '',
    country: '',
    last_name: '',
    first_name: '',
    phone: '',
  }
}

export function setAdressForCreate(address: Address): AddressToCreate {
  return {
    name: address.name,
    companyId: address.companyId,
    company: address.company,
    type: address.type,
    street: address.street,
    postcode: address.postcode,
    city: address.city,
    country: address.country,
    lastName: address.last_name,
    firstName: address.first_name,
    phone: address.phone,
  }
}

export function setAdressForUpdate(address: Address): AddressToUpdate {
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
    lastName: address.last_name,
    firstName: address.first_name,
    phone: address.phone,
  }
}
