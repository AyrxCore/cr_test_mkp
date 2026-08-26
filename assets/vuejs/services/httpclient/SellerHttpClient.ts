import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  Seller,
  SellerPromotion,
  MapApiResponse,
  StoreData,
} from '@/vuejs/types/Seller'

export default class SellerHttpClient extends BaseClientService {
  public fetchSellersByParams(params): Promise<Seller[]> {
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
    return this.apiClient.get(`sellers?${queryString}`).then((response) => {
      return response.data
    })
  }

  public getSeller(id: string): Promise<Seller> {
    return this.apiClient.get(`sellers/${id}`).then((response) => response.data)
  }

  public getSellerPromotions(id: string): Promise<SellerPromotion[]> {
    return this.apiClient
      .get(`sellers/${id}/promotions`)
      .then((response) => response.data)
  }


  /**
   * Récupère les données de la map (stores + catégories) depuis le back-end
   */
  public fetchMapData(
    categoryId: string | null,
    signal?: AbortSignal,
  ): Promise<MapApiResponse> {
    let url = 'partner-stores/map-data'
    if (categoryId) {
      url += `?categories=${categoryId}`
    }

    return this.apiClient
      .get<MapApiResponse>(url, { signal })
      .then((response) => response.data)
  }

  public fetchStoreDetail(id: string): Promise<StoreData> {
    return this.apiClient
      .get<StoreData>(`partner-stores/${id}/detail`)
      .then((response) => response.data)
  }
}
