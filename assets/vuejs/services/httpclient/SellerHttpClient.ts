import BaseClientService from '@/vuejs/services/BaseClientService'
import {
  Partner,
  Seller,
  SellerPromotion,
  MapApiResponse,
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

  public getSeller(id: number): Promise<Seller> {
    return this.apiClient.get(`sellers/${id}`).then((response) => response.data)
  }

  public getSellerPromotions(id: number): Promise<SellerPromotion[]> {
    return this.apiClient
      .get(`sellers/${id}/promotions`)
      .then((response) => response.data)
  }

  public fetchPartnerByUpplerId<T extends Partner>(
    upplerId: string,
  ): Promise<T | T[]> {
    return this.apiClient
      .get<T[]>(`partners?upplerId=${upplerId}`)
      .then((response) => {
        const data = response.data
        return Array.isArray(data) && data.length > 0 ? data[0] : data
      })
  }

  /**
   * Récupère les données de la map (stores + catégories) depuis le back-end
   */
  public fetchMapData(categoryId?: number): Promise<MapApiResponse> {
    let url = 'partner-stores/map-data'
    if (categoryId) {
      url += `?categoryId=${categoryId}`
    }

    return this.apiClient
      .get<MapApiResponse>(url)
      .then((response) => response.data)
  }
}
