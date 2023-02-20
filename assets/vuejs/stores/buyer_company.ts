import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import AddressHttpClient from '@/vuejs/services/httpclient/AddressHttpClient'
import { BuyerCompanyStoreState } from '@/vuejs/types/BuyerCompany'
import { Address } from '@/vuejs/types/Address'
import {
  getEmptyAddress,
  setAdressForCreate,
  setAdressForUpdate,
} from '@/vuejs/services/company'
import { useUserStore } from '@/vuejs/stores/user'
import router, { PageList } from '@/vuejs/router'

export const useBuyerCompanyStore = defineStore({
  id: 'company',
  state: (): BuyerCompanyStoreState => ({
    adresses: [],
    currentAddress: null,
    isloading: false,
  }),

  actions: {
    async getAdresses(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.adresses = await AddressHttpClient.get().getAdressesAsBuyer()
      } catch (error) {
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
    async createAddress(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        const addressToCreate = setAdressForCreate(this.currentAddress)
        await AddressHttpClient.get().createAdressesAsAdmin(addressToCreate)
        alertStore.setShow(
          'L\'adresse a été créée avec succès',
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
    async updateAddress(): Promise<void> {
      const userStore = useUserStore()
      const alertStore = useAlertStore()
      try {
        this.currentAddress.companyId = userStore.user.account.buyer.id
        await AddressHttpClient.get().updateAdressesAsAdmin(
          setAdressForUpdate(this.currentAddress),
        )
        alertStore.setShow(
          'Les modifications ont été enregistrées avec succès',
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
    async getAddress(id: number): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.currentAddress = await AddressHttpClient.get().getAdressAsAdmin(id)
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
