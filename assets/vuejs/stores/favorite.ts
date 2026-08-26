import { defineStore } from 'pinia'
import { Favorite } from '@/vuejs/types/Favorite'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import FavoriteHttpClient from '@/vuejs/services/httpclient/FavoriteHttpClient'
import { notifyError, notifySuccess } from '@/vuejs/services/utils'

export interface FavoriteStoreState {
  favorites?: Favorite[]
  favorite: Favorite
}

export const useFavoriteStore = defineStore('favorite', {
  state: (): FavoriteStoreState => ({
    favorites: [],
    favorite: null,
  }),

  actions: {
    async fetchFavorites(): Promise<void> {
      try {
        this.favorites = await FavoriteHttpClient.get().fetchList()
      } catch (error) {
        if (error.response.status === HttpStatusCodes.unauthorized) {
          notifyError(error.response.data.message)
        }
      }
    },
    async create(favorite: Favorite) {
      try {
        const newFavorite = await FavoriteHttpClient.get().create(favorite)
        notifySuccess('Votre liste a été créée')
        return newFavorite
      } catch (error) {
        if (error.response.status === 409) {
          throw error
        } else {
          notifyError(`La liste ${favorite.name} n'a pas pu être mise à jour`)
        }
      }
    },
    async findFavoriteById(id) {
      try {
        return await FavoriteHttpClient.get().findFavoriteById(id)
      } catch (_error) {
        // Ignored: favorite details may no longer exist
      }
    },

    async update(favorite: Favorite) {
      try {
        const updatedFavorite =
          await FavoriteHttpClient.get(true).update(favorite)
        notifySuccess(`La liste ${favorite.name} a été mise à jour`)
        return updatedFavorite
      } catch (error) {
        if (error.response.status === 422) {
          throw error
        } else {
          notifyError(`La liste ${favorite.name} n'a pas pu être mise à jour`)
        }
      }
    },
    async delete(id): Promise<void> {
      try {
        await FavoriteHttpClient.get().delete(id)
        notifySuccess('La liste a bien été supprimée')
      } catch (error) {
        notifyError(error.response.data.message)
      }
    },
    async addProduct(data) {
      try {
        await FavoriteHttpClient.get().addProduct(data)
      } catch (_error) {
        notifyError(
          "Impossible d'ajouter ce produit à cette liste car elle n'existe plus",
        )
      }
    },
    async removeProduct(favoriteProductId) {
      try {
        await FavoriteHttpClient.get().removeProduct(favoriteProductId)
        notifySuccess('Ce produit a été rétiré de la liste')
      } catch (_error) {
        notifyError("Une erreur est survenue lors de l'ajout")
      }
    },
    async moveProduct(favoriteProductId, favoriteId) {
      try {
        const response = await FavoriteHttpClient.get(true).moveProduct(
          favoriteProductId,
          { favoriteId },
        )
        notifySuccess(response.message)
      } catch (error) {
        notifyError(error.response.data.message)
      }
    },
    async deleteFavoriteAndMoveProductToOtherFavorite(id, idToReceive) {
      try {
        const response =
          await FavoriteHttpClient.get().moveProductToOtherFavorite(
            id,
            idToReceive,
          )
        await this.delete(id)
        notifySuccess(response.message)
      } catch (error) {
        notifyError(error.response.data.message)
      }
    },
  },
})
