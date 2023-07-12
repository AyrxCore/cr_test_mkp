import { defineStore } from 'pinia'
import {
  AuthenticateUserData,
  PasswordChangeRequest,
  UserStoreState,
} from '@/vuejs/types/User'

import UserHttpClient from '@/vuejs/services/httpclient/UserHttpClient'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import router, { PageList } from '@/vuejs/router'

export const useUserStore = defineStore({
  id: 'user',
  state: (): UserStoreState => ({
    user: null,
    editingInfo: [],
  }),

  actions: {
    async authenticate(userDatas: AuthenticateUserData): Promise<[]> {
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
    setEditingSubAccount(): void {
      // this.user.account.editingSubAccount = {...this.user.account.subaccount}
      this.editingInfo = {
        username: this.user.username,
        firstName: this.user.firstName,
        lastName: this.user.lastName,
        phone: this.user.account.phone,
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
        alertStore.setShow(
          "L'adresse de facturation par défaut a été modifiée avec succès",
          AlertType.success,
        )
      } catch (error) {
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
        alertStore.setShow(
          "L'adresse de livraison par défaut a été modifiée avec succès",
          AlertType.success,
        )
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
    async updateUserAccountEmail(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        const response = await UserHttpClient.get(true).updateUserAccountEmail({
          email: this.editingInfo.username,
          id: this.user.account.id,
        })
        alertStore.setShow(
          "La demande de modification d'email de contact a été engegistrée avec succès",
          AlertType.success,
        )
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
    async updateUserAccountDetails(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        console.log(this.editingInfo.phone)
        await UserHttpClient.get(true).updateUserAccountDetails(
          this.user.account.id,
          {
            lastName: this.editingInfo.lastName,
            firstName: this.editingInfo.firstName,
            phone: this.editingInfo.phone,
            id: this.user.account.id,
          },
        )
        this.user.lastName = this.editingInfo.lastName
        this.user.firstName = this.editingInfo.firstName
        this.user.account.phone = this.editingInfo.phone
        alertStore.setShow(
          'Les détails du profil ont été modifiés avec succès',
          AlertType.success,
        )
        router.push({
          name: PageList.ACCOUNT,
        })
      } catch (error) {
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
    async updateUserPassword(datas: PasswordChangeRequest): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get(true).updateUserPassword(datas)
        alertStore.setShow(
          'Le mot de passe a été modifié avec succès',
          AlertType.success,
        )
        router.push({
          name: PageList.ACCOUNT,
        })
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
  },

  getters: {
    isLogged(): boolean {
      return this.user !== null
    },
  },
})
