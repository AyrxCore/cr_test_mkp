import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { Product } from '@/vuejs/types/Product'
import AccordCadreHttpClient from '@/vuejs/services/httpclient/AccordCadreHttpClient'

export interface AccordCadreStoreState {
  accords_cadre: []
}

export const useAccordCadreStore = defineStore({
  id: 'accord_cadre',
  state: (): AccordCadreStoreState => ({
    accords_cadre: [],
  }),

  actions: {
    async findAccordsCadresByParams(params): Promise<[]> {
      try {
        this.accords_cadre = await AccordCadreHttpClient.get().findAccordsCadresByParams(params)
      } catch (error) {
        return []
      }
    },

    async findAccordCadreById(id) {
      const alertStore = useAlertStore()
      try {
        return await AccordCadreHttpClient.get().getAccordCadre(id)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }

    },
  },

  getters: {
    getAccordsCadre() :Array<Product>{
      return this.accords_cadre
    },
  },
})
