import BaseClientService from '@/vuejs/services/BaseClientService'

export default class CompanyHttpClient extends BaseClientService {
  public getAdressesAsBuyer<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('buyer/company/adresses')
      .then((response) => response.data)
  }
}
