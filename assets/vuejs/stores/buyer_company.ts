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
import { formatAddress } from '@/vuejs/services/utils'

export const useBuyerCompanyStore = defineStore({
  id: 'company',
  state: (): BuyerCompanyStoreState => ({
    addresses: [],
    currentAddress: null,
    isloading: false,
  }),

  actions: {
    async getAddresses(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.addresses = await AddressHttpClient.get().getAddressesAsBuyer()
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
        await AddressHttpClient.get().createAddressesAsAdmin(addressToCreate)
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
    async updateAddress(): Promise<void> {
      const userStore = useUserStore()
      const alertStore = useAlertStore()
      try {
        this.currentAddress.companyId = userStore.user.account.buyer.id
        await AddressHttpClient.get().updateAddressesAsAdmin(
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
    defaultAddress(): Address {
      return this.addresses.find((a: Address) => a.type === null)
    },
    shippingAddresses(): Address[] {
      return this.addresses.filter((a: Address) => a.type === 'shipping')
    },
    defaultShippingAddress(): Address {
      const userStore = useUserStore()
      if (!userStore.user.account.subaccount) return null
      return this.addresses.find(
        (address) =>
          address.id === userStore.user.account.subaccount.shipping_address,
      )
    },
    defaultShippingAddressFormatted(): string {
      return formatAddress(this.defaultShippingAddress)
    },
    billingAddresses(): Address[] {
      return this.addresses.filter((a: Address) => a.type === 'billing')
    },
    defaultBillingAddress(): Address {
      const userStore = useUserStore()
      if (!userStore.user.account.subaccount) return null
      return this.addresses.find(
        (address) =>
          address.id === userStore.user.account.subaccount.billing_address,
      )
    },
    defaultBillingAddressFormatted(): string {
      return formatAddress(this.defaultBillingAddress)
    },
  },
})
