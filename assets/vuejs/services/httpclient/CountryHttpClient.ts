import BaseClientService from '@/vuejs/services/BaseClientService'

export default class CountryHttpClient extends BaseClientService {
  public getCountriesAsBuyer<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('countries')
      .then((response) => response.data)
  }
}
