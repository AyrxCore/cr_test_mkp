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
import { Account } from '@/vuejs/types/Account'
import { notifyError, notifySuccess } from '@/vuejs/services/utils'
import { Account } from '@/vuejs/types/Account'

export const useUserStore = defineStore({
  id: 'user',
  state: (): UserStoreState => ({
    user: null,
    editingInfo: [],
  }),

  actions: {
    async authenticate(userData: AuthenticateUserData): Promise<Account[]> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get().getUserToken(userData)
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
    async getCurrentUserData(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.user = await UserHttpClient.get().getUserMe()
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow('Erreur technique', AlertType.danger)
      }
    },
    setEditingSubAccount(): void {
      this.editingInfo = {
        username: this.user.username,
        firstName: this.user.firstName,
        lastName: this.user.lastName,
        phone: this.user.account.phone,
      }
    },
    async updateUserDefaultBillingAddress(id: number): Promise<void> {
      try {
        await UserHttpClient.get(true).updateUserAddress({
          id: this.user.externalApiData.subaccount.id,
          accountId: this.user.account.id,
          billingAddressId: id,
        })
        this.user.externalApiData.subaccount.billing_address = id
        notifySuccess(
          `L'adresse de facturation par défaut a été modifiée avec succès`,
        )
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
      }
    },
    async updateUserDefaultShippingAddress(id: number): Promise<void> {
      try {
        await UserHttpClient.get(true).updateUserAddress({
          id: this.user.externalApiData.subaccount.id,
          accountId: this.user.account.id,
          shippingAddressId: id,
        })
        this.user.externalApiData.subaccount.shipping_address = id
        notifySuccess(
          `L'adresse de livraison par défaut a été modifiée avec succès`,
        )
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
      }
    },
    async updateUserAccountEmail(): Promise<void> {
      try {
        await UserHttpClient.get(true).updateUserAccountEmail({
          email: this.editingInfo.username,
          id: this.user.account.subaccount.id,
          accountId: this.user.account.id,
        })
        notifySuccess(
          `La demande de modification d'email de contact a été engegistrée avec succès`,
        )
        await this.getCurrentUserData()
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
      }
    },
    async updateUserAccountDetails(): Promise<void> {
      try {
        await UserHttpClient.get(true).updateUserAccountDetails({
          lastName: this.editingInfo.lastName,
          firstName: this.editingInfo.firstName,
          phone: this.editingInfo.phone,
          id: this.user.account.subaccount.id,
          accountId: this.user.account.id,
        })
        this.user.lastName = this.editingInfo.lastName
        this.user.firstName = this.editingInfo.firstName
        this.user.account.phone = this.editingInfo.phone

        notifySuccess('Les détails du profil ont été modifiés avec succès')

        await router.push({
          name: PageList.ACCOUNT,
        })
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
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
    async updateUserPassword(data: PasswordChangeRequest): Promise<void> {
      try {
        await UserHttpClient.get(true).updateUserPassword(data)

        notifySuccess('Le mot de passe a été modifié avec succès')

        await router.push({
          name: PageList.ACCOUNT,
        })
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
      }
    },
  },

  getters: {
    isLogged(): boolean {
      return this.user !== null
    },
  },
})
