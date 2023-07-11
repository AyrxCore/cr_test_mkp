import { Favorite } from '@/vuejs/types/Favorite'
import BaseClientService from '@/vuejs/services/BaseClientService'
import { User } from '@/vuejs/types/User'

export default class FavoriteHttpClient extends BaseClientService {
  public fetchList<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('favorites')
      .then((response) => response.data['hydra:member'])
  }

  public create<T extends []>(favorite: Favorite): Promise<T | Favorite> {
    return this.apiClient
      .post<T>('favorites', favorite)
      .then((response) => response.data)
  }

  public findFavoriteById<T extends []>(id): Promise<Favorite> {
    return this.apiClient
      .get(`favorites/${id}`)
      .then((response) => response.data)
  }

  public update<T extends []>(favorite: Favorite): Promise<T> {
    return this.apiClient
      .patch(`favorites/${favorite.id}`, {
        name: favorite.name,
        public: favorite.public,
      })
      .then((response) => response.data)
  }

  public delete<T extends []>(id): Promise<T> {
    return this.apiClient
      .delete(`favorites/${id}`)
      .then((response) => response.data)
  }

  public addProduct<T extends []>(data): Promise<T | Favorite> {
    return this.apiClient
      .postForm<T>('favorite-products', data)
      .then((response) => response.data)
  }

  public removeProduct<T extends []>(id): Promise<T | Favorite> {
    return this.apiClient
      .delete<T>(`favorite-products/${id}`)
      .then((response) => response.data)
  }

  public moveProduct<T extends []>(
    favoriteProductId,
    data,
  ): Promise<T | Favorite> {
    return this.apiClient
      .postForm<T>(`favorite-products/${favoriteProductId}/favorites`, data)
      .then((response) => response.data)
  }

  public moveProductToOtherFavorite<T extends []>(
    id,
    favoriteIdToReceive,
  ): Promise<T | Favorite> {
    return this.apiClient
      .postForm<T>('favorite-products/favorites', {
        favoriteId: id,
        favoriteIdToReceive,
      })
      .then((response) => response.data)
  }
}
