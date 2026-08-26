import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  Address,
  AddressToCreate,
} from '@/vuejs/types/Address'

export default class AddressHttpClient extends BaseClientService {
  public getAddressesAsBuyer<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('addresses')
      .then((response) => response.data)
  }

  public getAdressAsAdmin(id: string): Promise<Address> {
    return this.apiClient
      .get(`addresses/${id}`)
      .then((response) => response.data)
  }

  public createAddressesAsAdmin<T extends []>(
    address: AddressToCreate,
  ): Promise<T> {
    return this.apiClient
      .post<T>('addresses', address)
      .then((response) => response.data)
  }

  public updateAddressesAsAdmin<T extends []>(
    address: Address,
  ): Promise<T> {
    return this.apiClient
      .put<T>(`addresses/${address.id}`, address)
      .then((response) => response.data)
  }
}
