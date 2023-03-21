import BaseClientService from '@/vuejs/services/BaseClientService'
import { Seller } from '@/vuejs/types/Seller'
export default class SellerHttpClient extends BaseClientService {
  public fetchSellers<T extends []>(): Promise<T> {
    return this.apiClient.get<T>('sellers').then((response) => response.data)
  }

  public getSeller<T extends []>(id: number): Promise<Seller> {
    return this.apiClient.get(`sellers/${id}`).then((response) => response.data)
  }
}
