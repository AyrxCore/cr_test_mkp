import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { Product } from '@/vuejs/types/Product'
import AccordCadreHttpClient from '@/vuejs/services/httpclient/AccordCadreHttpClient'
import { AccountAccordCadre } from '@/vuejs/types/AccordCadre';

export interface AccordCadreStoreState {
  accords_cadre: [],
  account_accord_cadre: AccountAccordCadre
}

export const useAccordCadreStore = defineStore({
  id: 'accord_cadre',
  state: (): AccordCadreStoreState => ({
    accords_cadre: [],
    account_accord_cadre: null
  }),

  actions: {
    async findAccordsCadresByParams(params): Promise<[]> {
      try {
          return await AccordCadreHttpClient.get().findAccordsCadresByParams(params)
      } catch (error) {
        return []
      }
    },

    async getAccordCadreById(id) {
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
