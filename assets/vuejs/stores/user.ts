import { defineStore } from 'pinia'
import { AuthenticateUserDatas, User, UserStoreState } from '@/vuejs/types/User'
import UserHttpClient from '@/vuejs/services/httpclient/UserHttpClient'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'

export const useUserStore = defineStore({
  id: 'user',
  state: (): UserStoreState => ({
    token: '',
    user: null,
  }),

  actions: {
    async authenticate(userDatas: AuthenticateUserDatas): Promise<void> {
      const alertStore = useAlertStore()
      try {
        const authDatas = await UserHttpClient.get().getUserToken(userDatas)
        this.token = authDatas.token
        document.location.href = '/app'
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Identifiants erronnés', AlertType.danger)
      }
    },
  },

  getters: {
    getUser(): User {
      return this.user
    },
    getToken(): string {
      return this.token
    },
  },
})
