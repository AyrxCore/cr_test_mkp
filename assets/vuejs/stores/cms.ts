import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import CmsHttpClient from '@/vuejs/services/httpclient/CmsHttpClient'

export const useCmsStore = defineStore({
  id: 'cms',
  state: () => ({}),

  actions: {
    async getPageById(pageId) {
      const alertStore = useAlertStore()
      try {
        return await CmsHttpClient.get().getPageById(pageId)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
  },
})
