import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { Seller, SellerStoreState } from '@/vuejs/types/Seller'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'

export const useSellerStore = defineStore({
  id: 'seller',
  state: (): SellerStoreState => ({
    sellers: [],
  }),

  actions: {
    async init() {
      const alertStore = useAlertStore()
      try {
        if (this.sellers.length === 0) {
          this.sellers = await SellerHttpClient.get().fetchSellers()
        }
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async getSeller(id: number): Promise<Seller> {
      return await SellerHttpClient.get().getSeller(id)
    },
  },
  getters: {},
})
