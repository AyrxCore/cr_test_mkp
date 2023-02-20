import BaseClientService from '@/vuejs/services/BaseClientService'
import { Product } from '@/vuejs/types/Product'

export default class ProductHttpClient extends BaseClientService {
  public fetchProductsByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        'products',
        params,
      )
      .then((response) => response.data)
  }

  public getProduct<T extends []>(id: number): Promise<Product> {
    return this.apiClient
        .get(`product/${id}`)
        .then((response) => response.data)
  }
}
