import BaseClientService from '@/vuejs/services/BaseClientService'
import {Address, AddressToUpdate} from '@/vuejs/types/Address'

export default class CompanyHttpClient extends BaseClientService {
  public getAdressesAsBuyer<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('buyer/company/addresses')
      .then((response) => response.data)
  }

  public getAdressAsAdmin<T extends []>(id: number): Promise<Address> {
    return this.apiClient
        .get(`company/addresses/${id}`)
        .then((response) => response.data)
  }

  public createAdressesAsAdmin<T extends []>(address: Address): Promise<T> {
    return this.apiClient
      .post<T>('company/addresses', address)
      .then((response) => response.data)
  }

  public updateAdressesAsAdmin<T extends []>(address: AddressToUpdate): Promise<T> {
    return this.apiClient
        .put<T>(`company/addresses/${address.id}`, address)
        .then((response) => response.data)
  }
}
