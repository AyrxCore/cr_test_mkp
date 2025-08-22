import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { AlertType } from '@/vuejs/types/Alert'
import { SavedCart } from '@/vuejs/types/SavedCart'
import SavedCartHttpClient from '@/vuejs/services/httpclient/SavedCartHttpClient'
import { notifyError, notifySuccess } from '@/vuejs/services/utils'

export interface SavedCartStoreState {
  savedCarts?: SavedCart[]
}

export const useSavedCartStore = defineStore('savedCart', {
  state: (): SavedCartStoreState => ({
    savedCarts: [],
  }),

  actions: {
    async fetchSavedCarts() {
      const alertStore = useAlertStore()
      try {
        this.savedCarts = await SavedCartHttpClient.get().fetchList()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async create(savedCart: SavedCart) {
      try {
        await SavedCartHttpClient.get().create(savedCart)
        notifySuccess('Votre panier a été sauvegardé')
      } catch (error) {
        notifyError(
          'Une erreur est survenue lors de la création de votre panier',
        )
      }
    },
    async findSavedCartById(id) {
      return await SavedCartHttpClient.get().findSavedCartById(id)
    },

    async update(savedCart: SavedCart) {
      const alertStore = useAlertStore()
      try {
        await SavedCartHttpClient.get(true).update(savedCart)
        alertStore.setShow(
          `Le panier <strong>${savedCart.name}</strong> a été mis à jour`,
          AlertType.success,
        )
      } catch (error) {
        alertStore.setShow(
          `Le panier  <strong>${savedCart.name}</strong> n'a pas pu être mis à jour`,
          AlertType.danger,
        )
      }
    },
    async delete(id): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await SavedCartHttpClient.get().delete(id)
        alertStore.setShow('Le panier a bien été supprimé', AlertType.success)
      } catch (error) {
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }
    },
  },
})
