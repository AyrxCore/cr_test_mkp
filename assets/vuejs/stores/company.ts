import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import CompanyHttpClient from '@/vuejs/services/httpclient/CompanyHttpClient'
import { CompanyStoreState } from '@/vuejs/types/Company'

export const useCompanyStore = defineStore({
  id: 'company',
  state: (): CompanyStoreState => ({
    adresses: [],
    isloading: false,
  }),

  actions: {
    async getAdresses(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.adresses = await CompanyHttpClient.get().getAdressesAsBuyer()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
  },

  getters: {},
})
