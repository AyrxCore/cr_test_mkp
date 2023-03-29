import BaseClientService from '@/vuejs/services/BaseClientService'
import {Product} from '@/vuejs/types/Product'

export default class ProductHttpClient extends BaseClientService {
  public fetchProductsByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        'products',
        params,
      )
      .then((response) => response.data)
  }

  public fetchHomeProducts<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('home-products')
      .then((response) => response.data)
  }

  public findProductById<T extends []>(id: number): Promise<Product> {
    return this.apiClient
      .get(`product/${id}`)
      .then((response) => response.data)
  }

  public findVariantById<T extends []>(id: number): Promise<Product> {
    return this.apiClient
      .get(`variant/${id}`)
      .then((response) => response.data)
  }

  public findAccordsCadresByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        'accords-cadre',
        params,
      )
      .then((response) => response.data)
  }

  public findAccordCadreById<T extends []>(id: number): Promise<Product> {
    return this.apiClient
      .get(`accord-cadre/${id}`)
      .then((response) => response.data)
  }

  public updateAccountAccordsCadresByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .post<T>(`accord-cadre-subscription`, params)
      .then((response) => response.data)
  }
}
