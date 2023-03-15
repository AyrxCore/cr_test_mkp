import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  Address,
  AddressToCreate,
  AddressToUpdate,
} from '@/vuejs/types/Address'

export default class AddressHttpClient extends BaseClientService {
  public getAddressesAsBuyer<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('buyer/addresses')
      .then((response) => response.data)
  }

  public getAdressAsAdmin<T extends []>(id: number): Promise<Address> {
    return this.apiClient
      .get(`buyer/get-address/${id}`)
      .then((response) => response.data)
  }

  public createAddressesAsAdmin<T extends []>(
    address: AddressToCreate,
  ): Promise<T> {
    return this.apiClient
      .post<T>('buyer/create-address', address)
      .then((response) => response.data)
  }

  public updateAddressesAsAdmin<T extends []>(
    address: AddressToUpdate,
  ): Promise<T> {
    return this.apiClient
      .put<T>(`buyer/update-address/${address.id}`, address)
      .then((response) => response.data)
  }
}
