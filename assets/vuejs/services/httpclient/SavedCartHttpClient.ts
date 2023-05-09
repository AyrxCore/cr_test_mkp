import BaseClientService from '@/vuejs/services/BaseClientService'
import { SavedCart } from '@/vuejs/types/SavedCart'

export default class SavedCartHttpClient extends BaseClientService {
  public fetchList<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('saved-carts')
      .then((response) => response.data['hydra:member'])
  }

  public create<T extends []>(savedCart: SavedCart): Promise<T | SavedCart> {
    return this.apiClient
      .post<T>('saved-carts/create', savedCart)
      .then((response) => response.data)
  }

  public findSavedCartById<T extends []>(id): Promise<SavedCart> {
    return this.apiClient
      .get(`saved-carts/${id}/products`)
      .then((response) => response.data)
  }

  public update<T extends []>(savedCart: SavedCart): Promise<T> {
    return this.apiClient
      .patch(`saved-carts/update/${savedCart.id}`, {
        name: savedCart.name,
      })
      .then((response) => response.data)
  }

  public delete<T extends []>(id): Promise<T> {
    return this.apiClient
      .delete(`saved-carts/delete/${id}`)
      .then((response) => response.data)
  }
}
