import BaseClientService from '@/vuejs/services/BaseClientService'
import { Seller, SellerPromotion } from '@/vuejs/types/Seller'
export default class SellerHttpClient extends BaseClientService {
  public fetchSellers<T extends []>(): Promise<T> {
    return this.apiClient.get<T>('sellers').then((response) => response.data)
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
