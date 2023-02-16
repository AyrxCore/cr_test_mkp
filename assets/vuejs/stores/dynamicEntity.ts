import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import DynamicEntityHttpClient from '@/vuejs/services/httpclient/DynamicEntityHttpClient'
import { DynamicEntity } from '@/vuejs/types/DynamicEntity'

export interface DynamicEntityStoreState {
  dynamicsEntities: DynamicEntity[],
  currentDynamicEntityId: number
}

export const useDynamicEntityStore = defineStore({
  id: 'dynamicEntity',
  state: (): DynamicEntityStoreState => ({
    dynamicsEntities: [],
    currentDynamicEntityId: null
  }),

  actions: {
    async findDynamicsEntities(): Promise<[]> {
      try {
        const response = await DynamicEntityHttpClient.get().findDynamicsEntities()
        return response
      } catch (error) {
        return []
      }
    },
    async findDynamicsEntitiesByParams(params): Promise<[]> {
      try {
        return  await DynamicEntityHttpClient.get().findDynamicsEntitiesByParams(params)
      } catch (error) {
        return []
      }
    },

    async getDynamicEntity() {
      const alertStore = useAlertStore()
      if (this.currentDynamicEntityId) {
        try {
          return await DynamicEntityHttpClient.get().getDynamicEntity(this.currentDynamicEntityId)
        } catch (error) {
          error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
      return null

    },
  },

  getters: {
    getDynamicsEntities() :Array<DynamicEntity>{
      return this.dynamicsEntities
    },
  },
})
