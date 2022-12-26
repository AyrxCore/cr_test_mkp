import { defineStore } from 'pinia'
import { AuthenticateUserDatas, User, UserStoreState } from '@/vuejs/types/User'
import UserHttpClient from '@/vuejs/services/httpclient/UserHttpClient'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'

export const useUserStore = defineStore({
  id: 'user',
  state: (): UserStoreState => ({
    user: null,
  }),

  actions: {
    async authenticate(userDatas: AuthenticateUserDatas): Promise<[]> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get().getUserToken(userDatas)
        return await UserHttpClient.get().getUserAccounts()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        return []
      }
    },
    async selectUserAccount(id: string): Promise<boolean> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get().selectUserAccount(id)
        return true
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        return false
      }
    },
    async getCurrentUserDatas(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.user = await UserHttpClient.get().getUserMe()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
    async updateUserDefaultBillingAddress(id: number): Promise<void> {
      const alertStore = useAlertStore()

      try {
        await UserHttpClient.get(true).updateUserAddress({
          billingAddressId: id,
          id: this.user.account.subaccount.id,
        })
        this.user.account.subaccount.billing_address = id
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
    async updateUserDefaultShippingAddress(id: number): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get(true).updateUserAddress({
          shippingAddressId: id,
          id: this.user.account.subaccount.id,
        })
        this.user.account.subaccount.shipping_address = id
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
    async logout(): Promise<boolean> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get().logout()
        return true
      } catch (error) {
        alertStore.setShow('Déconnexion impossible', AlertType.danger)
      }
      return false
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
      return this.user !== null
    },
  },
})
