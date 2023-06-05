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
      .post<T>('favorites/create', favorite)
      .then((response) => response.data)
  }

  public findFavoriteById<T extends []>(id): Promise<Favorite> {
    return this.apiClient
      .get(`favorites/${id}/products`)
      .then((response) => response.data)
  }

  public update<T extends []>(favorite: Favorite): Promise<T> {
    return this.apiClient
      .patch(`favorites/update/${favorite.id}`, {
        name: favorite.name,
        public: favorite.public,
      })
      .then((response) => response.data)
  }

  public delete<T extends []>(id): Promise<T> {
    return this.apiClient
      .delete(`favorites/delete/${id}`)
      .then((response) => response.data)
  }

  public addItem<T extends []>(data): Promise<T | Favorite> {
    return this.apiClient
      .postForm<T>('favorites/item/add', data)
      .then((response) => response.data)
  }

  public removeItem<T extends []>(
    favoriteId,
    productId,
    variantId,
  ): Promise<T | Favorite> {
    return this.apiClient
      .delete<T>(
        `favorites/item/remove/${favoriteId}/${productId}/${variantId}`,
      )
      .then((response) => response.data)
  }

  public moveItem<T extends []>(data): Promise<T | Favorite> {
    return this.apiClient
      .postForm<T>('favorites/item/move', data)
      .then((response) => response.data)
  }

  public deleteFavoriteAndMoveProductToOtherFavorite<T extends []>(
    id,
    idToMove,
  ): Promise<T | Favorite> {
    return this.apiClient
      .postForm<T>(
        'favorites/items-move-to-other-favorite-and-delete-favorite',
        {
          favoriteId: id,
          favoriteIdToReceive: idToMove,
        },
      )
      .then((response) => response.data)
  }
}
