import { defineStore } from 'pinia'
import { AuthenticateUserDatas, User, UserStoreState } from '@/vuejs/types/User'
import UserHttpClient from '@/vuejs/services/httpclient/UserHttpClient'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import router, { PageList } from '@/vuejs/router'

export const useUserStore = defineStore({
  id: 'user',
  state: (): UserStoreState => ({
    token: null,
    user: null,
  }),

  actions: {
    async authenticate(
      userDatas: AuthenticateUserDatas,
      redirectToApp = false,
    ): Promise<boolean> {
      const alertStore = useAlertStore()
      try {
        const authDatas = await UserHttpClient.get().getUserToken(userDatas)
        this.token = authDatas.token
        redirectToApp && (document.location.href = '/app')
        return true
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Identifiants erronnés', AlertType.danger)
        return false
      }
    },
    async getCurrentUserDatas(): Promise<void> {
      this.user = await UserHttpClient.get().getUserMe()
    },
  },

  getters: {
    getUser(): User {
      return this.user
    },
    getToken(): string {
      return this.token
    },
    isLogged(): boolean {
      return this.token !== null
    },
  },
})
