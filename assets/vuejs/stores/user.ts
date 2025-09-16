import { defineStore } from 'pinia'
import router, { PageList } from '@/vuejs/router'
import { useAlertStore } from '@/vuejs/stores/alert'
import {
  AuthenticateUserData,
  LoginResponse,
  PasswordChangeRequest,
  UserLocation,
  UserStoreState,
} from '@/vuejs/types/User'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { Account } from '@/vuejs/types/Account'
import { AdherentTarifShowcase } from '@/vuejs/types/AdherentTarifShowcase'
import { notifyError, notifySuccess } from '@/vuejs/services/utils'
import { getCookie } from '@/vuejs/services/utils'
import UserHttpClient from '@/vuejs/services/httpclient/UserHttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import { useCartStore } from './cart'

export const useUserStore = defineStore('user', {
  state: (): UserStoreState => ({
    user: null,
    editingInfo: [],
    isNeoAutoLogin: getCookie('neoAutoLogin') === 'true',
    userLocation: null,
  }),

  actions: {
    async authenticate(userData: AuthenticateUserData): Promise<Account[]> {
      const alertStore = useAlertStore()
      try {
        await UserHttpClient.get().getUserToken(userData)
        const accounts = await UserHttpClient.get().getUserAccounts()

        if (!accounts.length) {
          alertStore.setShow(
            getErrorMessage(LoginResponse.UserEmptyAccount),
            AlertType.danger,
          )

          return []
        } else {
          return accounts
        }
      } catch (error) {
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
        await UserHttpClient.get()
          .logout()
          .catch(() => {})

        this.user = null
        alertStore.setShow(getErrorMessage(''), AlertType.danger)
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
          "L'adresse de facturation par défaut a été modifiée avec succès",
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
          "L'adresse de livraison par défaut a été modifiée avec succès",
        )
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
      }
    },
    updateCartAddressesWithDefault(): void {
      const cartStore = useCartStore()
      cartStore.updateCartAddress({
        cartId: cartStore.cart.id,
        billingAddressId: this.user.externalApiData.subaccount.billing_address,
        shippingAddressId:
          this.user.externalApiData.subaccount.shipping_address,
      })
    },
    async updateUserAccountEmail(): Promise<void> {
      try {
        await UserHttpClient.get(true).updateUserAccountEmail({
          email: this.editingInfo.username.toLowerCase(),
          id: this.user.externalApiData.subaccount.id,
          accountId: this.user.account.id,
        })
        notifySuccess(
          "La demande de modification d'email de contact a été enregistrée avec succès",
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
          id: this.user.externalApiData.subaccount.id,
          accountId: this.user.account.id,
        })
        this.user.lastName = this.editingInfo.lastName
        this.user.firstName = this.editingInfo.firstName
        this.user.account.phone = this.editingInfo.phone

        notifySuccess('Les détails du profil ont été modifiés avec succès')

        await router.push({ name: PageList.ACCOUNT })
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

        await router.push({ name: PageList.ACCOUNT })
      } catch (error) {
        notifyError(
          'Une erreur est survenue, veuillez contacter le service technique',
        )
      }
    },
    updateContactRequested(accordId: string): void {
      const showcase =
        this.user?.account?.adherent?.adherentTarifShowcases.find(
          (showcase) => showcase.accordId === accordId,
        )
      if (showcase) {
        showcase.contactRequested = true
        this.user = { ...this.user }
      }
    },
    setGeolocationError(error: string): void {
      if (!this.userLocation) {
        this.userLocation = { lat: 0, lng: 0, timestamp: 0, error: error }
      } else {
        this.userLocation.error = error || null
      }

      localStorage.setItem('userLocation', JSON.stringify(this.userLocation))
    },
    saveUserLocation(location: { lat: number; lng: number }): void {
      const isParis = location.lat === 48.8566 && location.lng === 2.3522

      if (isParis) {
        this.userLocation = {
          ...location,
          timestamp: Date.now(),
          error: this.userLocation?.error || null,
        }
      } else {
        this.userLocation = { ...location, timestamp: Date.now(), error: null }
      }

      localStorage.setItem('userLocation', JSON.stringify(this.userLocation))
    },
    loadUserLocation(): void {
      try {
        const savedLocation = localStorage.getItem('userLocation')
        if (savedLocation) {
          const parsed = JSON.parse(savedLocation) as UserLocation

          if (parsed.timestamp > Date.now() - 24 * 60 * 60 * 1000) {
            this.userLocation = parsed
          } else {
            localStorage.removeItem('userLocation')
          }
        }
      } catch (error) {
        console.warn(
          'Erreur lors du chargement de la position sauvegardée',
          error,
        )
      }
    },
    getGeolocationErrorMessage(): string {
      return this.userLocation?.error || ''
    },
  },
  getters: {
    isLogged(): boolean {
      return this.user !== null
    },
    adherentTarifShowcases(): AdherentTarifShowcase[] {
      return this.user?.account?.adherent?.adherentTarifShowcases || []
    },
    isGeolocationAvailable(): boolean {
      return (
        this.userLocation !== null &&
        this.userLocation.timestamp > Date.now() - 24 * 60 * 60 * 1000
      )
    },
    hasGeolocationError(): boolean {
      return (
        this.userLocation?.error !== null && this.userLocation?.error !== ''
      )
    },
    isShouldHideStellantisModal(): boolean {
      return this.user?.account?.adherent?.shouldHideStellantisModal || false
    },
  },
})
