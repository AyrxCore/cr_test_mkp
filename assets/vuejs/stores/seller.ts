import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { Seller, SellerStoreState } from '@/vuejs/types/Seller'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'
import AccordCadreHttpClient from '@/vuejs/services/httpclient/AccordCadreHttpClient';

export const useSellerStore = defineStore({
  id: 'seller',
  state: (): SellerStoreState => ({
    sellers: [],
  }),

  actions: {
    async getSellers(): Promise<Seller[]> {
      const alertStore = useAlertStore()
      try {
        return await SellerHttpClient.get().fetchSellers()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async getSellerById(id) {
      const alertStore = useAlertStore()
      try {
        return await SellerHttpClient.get().getSeller(id)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }

    },
  }
})
