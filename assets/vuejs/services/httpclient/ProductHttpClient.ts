import BaseClientService from '@/vuejs/services/BaseClientService'
import { Product } from '@/vuejs/types/Product'

export type ProductPropertyParams = {
  property_id: number
  value: string
}

export const ProductsParams = {
  properties: [] as ProductPropertyParams[],
  categories: [],
  cache_key: String,
}

export default class ProductHttpClient extends BaseClientService {
  public findProductsByParams<T extends []>(params): Promise<T> {
    return this.apiClient
      .postForm<T>(
        `products/${params.cache_key}`,
        {
              categories: params.categories,
              properties: params.properties,
            }
      )
      .then((response) => response.data)
  }

  public getProduct<T extends []>(id: number): Promise<Product> {
    return this.apiClient
        .get(`product/${id}`)
        .then((response) => response.data)
  }
}
