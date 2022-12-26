import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import CompanyHttpClient from '@/vuejs/services/httpclient/CompanyHttpClient'
import { CompanyStoreState } from '@/vuejs/types/Company'
import { Address } from '@/vuejs/types/Address'
import {
  getEmptyAddress,
  setAdressForCreate,
  setAdressForUpdate,
} from '@/vuejs/services/company'
import { useUserStore } from '@/vuejs/stores/user'
import router, { PageList } from '@/vuejs/router'

export const useCompanyStore = defineStore({
  id: 'company',
  state: (): CompanyStoreState => ({
    adresses: [],
    currentAddress: null,
    isloading: false,
  }),

  actions: {
    async getAdresses(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.adresses = await CompanyHttpClient.get().getAdressesAsBuyer()
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    setCurrentAddress(address: Address): void {
      this.currentAddress = address
    },
    initNewAddress(type: string): void {
      const userStore = useUserStore()
      this.currentAddress = getEmptyAddress(
        userStore.user.account.buyer.id,
        type,
      )
    },
    async createAddress(): void {
      const alertStore = useAlertStore()
      try {
        await CompanyHttpClient.get().createAdressesAsAdmin(
          setAdressForCreate(this.currentAddress),
        )
        alertStore.setShow(
          "L'adresse a été créée avec succès",
          AlertType.success,
        )
        router.push({ name: PageList.ADDRESSES })
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async updateAddress(): void {
      const userStore = useUserStore()
      const alertStore = useAlertStore()
      try {
        this.currentAddress.companyId = userStore.user.account.buyer.id
        await CompanyHttpClient.get().updateAdressesAsAdmin(
          setAdressForUpdate(this.currentAddress),
        )
        alertStore.setShow(
          'Les modifications ont été enregistrées avec succès',
          AlertType.success,
        )
        router.push({ name: PageList.ADDRESSES })
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async getAddress(id: number): void {
      const alertStore = useAlertStore()
      try {
        this.currentAddress = await CompanyHttpClient.get().getAdressAsAdmin(id)
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
    getCurrentAddress(): Address {
      return this.currentAddress
    },
    getDefaultShippingAddress(): Address {
      const userStore = useUserStore()
      return this.adresses.find(
        (address) =>
          address.id === userStore.user.account.subaccount.shipping_address,
      )
    },
    getDefaultBillingAddress(): Address {
      const userStore = useUserStore()
      return this.adresses.find(
        (address) =>
          address.id === userStore.user.account.subaccount.billing_address,
      )
    },
  },
})
