import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import AddressHttpClient from '@/vuejs/services/httpclient/AddressHttpClient'
import { Address, AddressStoreState } from '@/vuejs/types/Address'
import router, { PageList } from '@/vuejs/router'
import {
  notifyError,
  notifySuccess,
} from '@/vuejs/services/utils'
// TODO: Réactiver après le go-live (adresse par défaut)
// import { formatAddress } from '@/vuejs/services/utils'
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'
import { v4 as uuidv4 } from 'uuid'
import {
  setAddressForCreate,
  setAddressForUpdate,
} from '@/vuejs/services/company'

export const useAddressStore = defineStore('address', {
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
      this.currentAddress = {
        id: uuidv4(),
        externalId: null,
        fullName: '',
        address: '',
        zipcode: '',
        city: '',
        country: '',
        phone: '',
        shipping: type === ADDRESS_SHIPPING,
        billing: type === ADDRESS_BILLING,
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
      try {
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
    async getAddress(id: string): Promise<void> {
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
    shippingAddresses(): Address[] {
      return this.addresses.filter((a: Address) => a.shipping)
    },
    billingAddresses(): Address[] {
      return this.addresses.filter((a: Address) => a.billing)
    },
    // TODO: Réactiver après le go-live quand la fonctionnalité adresse par défaut sera disponible côté Djust
    // defaultShippingAddress(): Address {
    //   return this.addresses.find((a: Address) => a.shipping) ?? null
    // },
    // defaultShippingAddressFormatted(): string {
    //   return formatAddress(this.defaultShippingAddress)
    // },
    // defaultBillingAddress(): Address {
    //   return this.addresses.find((a: Address) => a.billing) ?? null
    // },
    // defaultBillingAddressFormatted(): string {
    //   return formatAddress(this.defaultBillingAddress)
    // },
  },
})
