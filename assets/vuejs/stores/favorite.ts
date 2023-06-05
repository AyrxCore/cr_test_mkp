import { defineStore } from 'pinia'
import { Favorite } from '@/vuejs/types/Favorite'
import { useAlertStore } from '@/vuejs/stores/alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { AlertType } from '@/vuejs/types/Alert'
import FavoriteHttpClient from '@/vuejs/services/httpclient/FavoriteHttpClient'
import { notifyError, notifySuccess } from '@/vuejs/services/utils'

export interface FavoriteStoreState {
  favorites?: Favorite[]
  favorite: Favorite
}

export const useFavoriteStore = defineStore({
  id: 'favorite',
  state: (): FavoriteStoreState => ({
    favorites: [],
    favorite: null,
  }),

  actions: {
    async fetchFavorites(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.favorites = await FavoriteHttpClient.get().fetchList()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async create(favorite: Favorite) {
      const alertStore = useAlertStore()
      try {
        const newFavorite = await FavoriteHttpClient.get().create(favorite)
        alertStore.setShow('Votre liste a été créée', AlertType.success)
        return newFavorite
      } catch (error) {
        alertStore.setShow(
          'Une erreur est survenue lors de la création de votre liste, veuillez essayer ultérieurement ou contacter le service client',
          AlertType.danger,
        )
      }
    },
    async findFavoriteById(id) {
      return await FavoriteHttpClient.get().findFavoriteById(id)
    },

    async update(favorite: Favorite) {
      const alertStore = useAlertStore()
      try {
        const updatedFavorite = await FavoriteHttpClient.get(true).update(
          favorite,
        )
        alertStore.setShow(
          `La liste <strong>${favorite.name}</strong> a été mise à jour`,
          AlertType.success,
        )
        return updatedFavorite
      } catch (error) {
        alertStore.setShow(
          `La liste  <strong>${favorite.name}</strong> n'a pas pu être mise à jour`,
          AlertType.danger,
        )
      }
    },
    async delete(id): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await FavoriteHttpClient.get().delete(id)
        alertStore.setShow('La liste a bien été supprimée', AlertType.success)
      } catch (error) {
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }
    },
    async addItem(data) {
      try {
        await FavoriteHttpClient.get().addItem(data)
        notifySuccess(`Le produit ${data.productName} a été ajouté`)
      } catch (error) {
        notifyError(
          "Impossible d'ajouter ce produit à cette liste car elle n'existe plus",
        )
      }
    },
    async removeItem(favoriteId, productId, variantId) {
      const alertStore = useAlertStore()
      try {
        await FavoriteHttpClient.get().removeItem(
          favoriteId,
          productId,
          variantId,
        )
        alertStore.setShow(
          'Le produit a été retiré de la liste',
          AlertType.success,
        )
      } catch (error) {
        alertStore.setShow(
          "Une erreur est survenue lors de l'ajout",
          AlertType.danger,
        )
      }
    },
    async moveItem(data) {
      const alertStore = useAlertStore()
      try {
        await FavoriteHttpClient.get().moveItem(data)
        alertStore.setShow(
          'Le produit a été retiré de la liste',
          AlertType.success,
        )
      } catch (error) {
        alertStore.setShow(
          "Une erreur est survenue lors de l'ajout",
          AlertType.danger,
        )
      }
    },
    async deleteFavoriteAndMoveProductToOtherFavorite(id, idToReceive) {
      const alertStore = useAlertStore()
      try {
        await FavoriteHttpClient.get().deleteFavoriteAndMoveProductToOtherFavorite(
          id,
          idToReceive,
        )
        alertStore.setShow(
          'La liste a bien été supprimée et les produits déplacés',
          AlertType.success,
        )
      } catch (error) {
        alertStore.setShow(
          "Une erreur est survenue lors de l'ajout",
          AlertType.danger,
        )
      }
    },
  },
})
