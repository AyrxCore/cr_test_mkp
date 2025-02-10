import BaseClientService from '@/vuejs/services/BaseClientService'
import { Seller, SellerPromotion } from '@/vuejs/types/Seller'
import { ProductCollection } from '@/vuejs/types/Product'

export default class SellerHttpClient extends BaseClientService {
  public fetchSellersByParams<T extends []>(params): Promise<Seller[]> {
    const queryString = Object.keys(params)
      .map((key) => {
        if (typeof params[key] === 'object') {
          // Si la valeur est un objet, la sérialiser en JSON
          return `${encodeURIComponent(key)}=${encodeURIComponent(
            JSON.stringify(params[key]),
          )}`
        } else {
          // Si la valeur n'est pas un objet, l'inclure telle quelle
          return `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`
        }
      })
      .join('&')
    return this.apiClient.get<T>(`sellers?${queryString}`).then((response) => {
      return response.data
    })
  }

  public getSeller<T extends []>(id: number): Promise<Seller> {
    return this.apiClient.get(`sellers/${id}`).then((response) => response.data)
  }

  public getSellerPromotions<T extends []>(
    id: number,
  ): Promise<SellerPromotion[]> {
    return this.apiClient
      .get(`sellers/${id}/promotions`)
      .then((response) => response.data)
  }
}
