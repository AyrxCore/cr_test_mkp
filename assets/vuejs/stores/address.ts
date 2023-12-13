import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import AddressHttpClient from '@/vuejs/services/httpclient/AddressHttpClient'
import { Address, AddressStoreState } from '@/vuejs/types/Address'
import { useUserStore } from '@/vuejs/stores/user'
import router, { PageList } from '@/vuejs/router'
import {
  formatAddress,
  notifyError,
  notifySuccess,
} from '@/vuejs/services/utils'
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'
import { v4 as uuidv4 } from 'uuid'
import {
  setAddressForCreate,
  setAddressForUpdate,
} from '@/vuejs/services/company'

export const useAddressStore = defineStore({
  id: 'address',
  state: (): AddressStoreState => ({
    addresses: [],
    currentAddress: null,
    isLoading: false,
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
      this.currentAddress = {
        id: uuidv4(),
        name: '',
        companyId: userStore.user.externalApiData?.buyer.id,
        company: '',
        type,
        street: '',
        postcode: '',
        city: '',
        country: 83,
        lastName: '',
        firstName: '',
        phone: '',
      }
    },
    async createAddress(): Promise<void> {
      try {
        await AddressHttpClient.get().createAddressesAsAdmin(
          setAddressForCreate(this.currentAddress),
        )
        notifySuccess("L'adresse a bien été enregistrée")

        router.push({ name: PageList.ADDRESSES })
      } catch (error) {
        notifyError(
          "Une erreur est survenue lors de l'enregistrement. Veuillez contacter le service adhérent",
        )
      }
    },
    async updateAddress(): Promise<void> {
      const userStore = useUserStore()
      try {
        this.currentAddress.companyId = userStore.user.externalApiData?.buyer.id
        await AddressHttpClient.get().updateAddressesAsAdmin(
          setAddressForUpdate(this.currentAddress),
        )
        notifySuccess('Les modifications ont été enregistrées avec succès')

        router.push({ name: PageList.ADDRESSES })
      } catch (error) {
        notifyError(
          "Une erreur est survenue lors de l'enregistrement. Veuillez contacter le service adhérent",
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
      return this.addresses.filter((a: Address) => a.type === ADDRESS_SHIPPING)
    },
    defaultShippingAddress(): Address {
      const userStore = useUserStore()
      if (!userStore.user?.externalApiData?.subaccount) {
        return null
      }

      return this.addresses.find(
        (address: Address) =>
          address.id ===
          userStore.user.externalApiData.subaccount.shipping_address,
      )
    },
    defaultShippingAddressFormatted(): string {
      return formatAddress(this.defaultShippingAddress)
    },
    billingAddresses(): Address[] {
      return this.addresses.filter((a: Address) => a.type === ADDRESS_BILLING)
    },
    defaultBillingAddress(): Address {
      const userStore = useUserStore()
      if (!userStore.user?.externalApiData?.subaccount) {
        return null
      }

      return this.addresses.find(
        (address: Address) =>
          address.id ===
          userStore.user.externalApiData.subaccount.billing_address,
      )
    },
    defaultBillingAddressFormatted(): string {
      return formatAddress(this.defaultBillingAddress)
    },
  },
})
