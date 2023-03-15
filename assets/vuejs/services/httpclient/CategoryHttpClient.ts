import BaseClientService from '@/vuejs/services/BaseClientService'

export default class CategoryHttpClient extends BaseClientService {
  public getCategories<T extends []>(): Promise<T> {
    return this.apiClient
      .post<T>('categories-list')
      .then((response) => response.data)
  }
}
