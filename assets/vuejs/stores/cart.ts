import { defineStore } from 'pinia'

import CartHttpClient from '@/vuejs/services/httpclient/CartHttpClient'
import CompanyHttpClient from '@/vuejs/services/httpclient/CompanyHttpClient'
import { notifyError, notifySuccess } from '@/vuejs/services/utils'

import {
  AdyenInitiatePaymentPayload,
  AdyenInitiatePaymentResponse,
  AdyenPaymentMethod,
  AdyenPaymentMethodType,
  AdyenSubmitDetailsPayload,
  CartAddressesUpdate,
  CartPaymentMethodUpdated,
  CartPaymentSepaUpdated,
  CartStoreState,
  SepaData,
} from '@/vuejs/types/Cart'
import { CART_LINE_ACTIONS, PRODUCT_FDP_PREFIX } from '@/vuejs/services/const.ts'

export const useCartStore = defineStore('cart', {
  state: (): CartStoreState => ({
    cart: null,
    termsOfSales: [],
    newlyAddedProducts: [],
    modifyingCart: false,
    companyMandates: [],
    selectedSepa: null,
    adyenPaymentMethods: [],
    storedPaymentMethods: [],
    enableCreditCardStorage: false,
    bankTransferInfo: null,
  }),

  actions: {
    async getCart(): Promise<void> {
      try {
        this.cart = await CartHttpClient.get().getCart()
        this.newlyAddedProducts = []
      } catch (_error) {
        this.cart = {}
        notifyError(
          'Une erreur est survenue lors du chargement du panier, merci de contacter un administrateur.',
        )
      }
    },
    async addProductsToCart(
      data: { offerPriceId: string; quantity: number }[],
    ): Promise<void> {
      try {
        if (!this.cart?.id) {
          await this.getCart()
        }

        if (!this.cart?.id) {
          throw new Error()
        }

        const payload = data.map((line) => ({
          offerPriceId: line.offerPriceId,
          quantity: line.quantity,
          action: CART_LINE_ACTIONS.ADD_QUANTITY,
        }))

        try {
          await CartHttpClient.get().updateProductsToCart(this.cart.id, payload)
        } catch (_error) {
          this.cart = null
          await this.getCart()

          if (!this.cart?.id) {
            throw new Error(undefined, { cause: _error })
          }

          await CartHttpClient.get().updateProductsToCart(this.cart.id, payload)
        }

        notifySuccess('La référence du produit a été ajoutée au panier')
      } catch (_error) {
        notifyError(
          "L'ajout au panier est impossible, merci de contacter un administrateur.",
        )
        throw new Error(undefined, { cause: _error })
      }
    },
    async updateProductsToCart(
      data: { offerPriceId: string; quantity: number }[],
    ): Promise<void> {
      try {
        if (!this.cart?.id) {
          throw new Error()
        }

        const payload = data.map((line) => ({
          offerPriceId: line.offerPriceId,
          quantity: line.quantity,
          action: CART_LINE_ACTIONS.REPLACE_QUANTITY,
        }))

        await CartHttpClient.get().updateProductsToCart(this.cart.id, payload)

        notifySuccess('La quantité de produit a été modifiée dans le panier')
      } catch (_error) {
        notifyError(
          "L'édition du panier est impossible, merci de contacter un administrateur.",
        )
        throw new Error(undefined, { cause: _error })
      }
    },
    async removeProductsToCart(
      data: { offerPriceId: string }[],
    ): Promise<void> {
      try {
        if (!this.cart?.id) {
          throw new Error()
        }

        const offerPriceIds = data.map((line) => line.offerPriceId)
        await CartHttpClient.get().deleteCartLines(this.cart.id, offerPriceIds)
        notifySuccess('Le produit a été retiré du panier')
      } catch (_error) {
        notifyError(
          "L'édition du panier est impossible, merci de contacter un administrateur.",
        )
        throw new Error(undefined, { cause: _error })
      }
    },
    async syncProductsFdp(): Promise<void> {
      if (!this.cart?.id) return
      try {
        await CartHttpClient.get().syncProductsFdp(String(this.cart.id))
        await this.getCart()
      } catch (_error) {
        notifyError(
          'Une erreur est survenue lors de la synchronisation des frais de port.',
        )
      }
    },

    async syncCart(): Promise<string[]> {
      if (!this.cart?.id) return []
      try {
        const result = await CartHttpClient.get().syncCart(String(this.cart.id))
        return result.removedOfferPriceIds ?? []
      } catch (_error) {
        return []
      }
    },
    async updateCartAddress(data: CartAddressesUpdate): Promise<void> {
      try {
        await CartHttpClient.get(true).updateCartAddresses(data)
        this.cart.shippingAddressExternalId = data.shippingAddressExternalId
        this.cart.billingAddressExternalId = data.billingAddressExternalId
      } catch (_error) {
        notifyError(
          "Une erreur est survenue lors du choix de l'adresse, merci de contacter un administrateur.",
        )
      }
    },
    async updateCartPaymentMethod(
      paymentMethodType: AdyenPaymentMethodType,
    ): Promise<CartPaymentMethodUpdated> {
      try {
        return await CartHttpClient.get(true).updateCartPaymentMethod({
          cartId: this.cart.id,
          paymentMethodType,
        })
      } catch (_error) {
        notifyError(
          'Une erreur est survenue lors du choix de la méthode de paiement, merci de contacter un administrateur.',
        )
      }
    },
    async updateCartPaymentSepaInfos({
      iban,
      bic,
      ownerName,
      phone,
      mandateId,
    }: SepaData): Promise<CartPaymentSepaUpdated> {
      try {
        return await CartHttpClient.get(true).updateCartPaymentSepaInfos({
          cartId: this.cart.id,
          iban,
          bic,
          ownerName,
          phone,
          mandateId,
        })
      } catch (error) {
        notifyError(
          'Une erreur est survenue lors de la sauvegarde de vos informations, merci de contacter un administrateur.',
        )
        throw error?.response?.data?.errors
      }
    },
    async getCompanyMandates(): Promise<void> {
      try {
        this.companyMandates =
          await CompanyHttpClient.get().getExistingMandates()
      } catch (_error) {
        notifyError(
          'Une erreur est survenue lors du chargement des mandats, merci de contacter un administrateur.',
        )
      }
    },
    async fetchAdyenPaymentMethods(): Promise<void> {
      try {
        if (!this.cart?.id) return
        const result = await CartHttpClient.get().getPaymentMethods(String(this.cart.id))
        this.adyenPaymentMethods = result.paymentMethods
        this.storedPaymentMethods = result.storedPaymentMethods ?? []
        this.enableCreditCardStorage = result.enableCreditCardStorage
      } catch (_error) {
        notifyError(
          'Une erreur est survenue lors de la récupération des moyens de paiement, merci de contacter un administrateur.',
        )
      }
    },
    async initiateAdyenPayment(payload: AdyenInitiatePaymentPayload): Promise<AdyenInitiatePaymentResponse | null> {
      try {
        const result = await CartHttpClient.get().initiatePayment(payload)

        if (result.action?.type === 'bankTransfer') {
          this.bankTransferInfo = {
            beneficiary: result.action.beneficiary as string,
            iban: result.action.iban as string,
            bic: result.action.bic as string,
            reference: payload.reference,
            totalAmount: result.action.totalAmountValue as string,
          }
        }

        return result
      } catch (_error) {
        notifyError(
          "Une erreur est survenue lors de l'initialisation du paiement, merci de contacter un administrateur.",
        )
        return null
      }
    },
    async submitAdyenPaymentDetails(payload: AdyenSubmitDetailsPayload): Promise<AdyenInitiatePaymentResponse | null> {
      try {
        const result = await CartHttpClient.get().submitPaymentDetails(payload)
        return result
      } catch (_error) {
        notifyError(
          'Une erreur est survenue lors de la validation du paiement, merci de contacter un administrateur.',
        )
        return null
      }
    },
    resetDropinState(): void {
      this.bankTransferInfo = null
    },
    resetPaymentMethods(): void {
      this.adyenPaymentMethods = []
      this.storedPaymentMethods = []
      this.enableCreditCardStorage = false
    },
    async updateCustomerInfoInLogisticOrders(): Promise<void> {
      if (!this.cart?.id) return
      await CartHttpClient.get().updateCustomerInfoInLogisticOrders(String(this.cart.id))
    },
    async updateEcoTaxInLogisticOrders(): Promise<void> {
      if (!this.cart?.id) return
      try {
        await CartHttpClient.get().updateEcoTaxInLogisticOrders(String(this.cart.id))
      } catch (_error) {
        notifyError('Une erreur est survenue lors de la mise à jour de l\'éco-participation.')
      }
    },
    forceEmptyCart(): void {
      if (this.cart) {
        this.cart.id = null
        this.cart.productCount = 0
        this.cart.cartOrders = []
      }
    },
  },

  getters: {
    nbProducts(): number {
      return (this.cart?.cartOrders ?? []).reduce(
        (sum, order) =>
          sum +
          order.products.reduce(
            (orderSum, p) =>
              p.externalId?.startsWith(PRODUCT_FDP_PREFIX)
                ? orderSum
                : orderSum + (p.quantity ?? 0),
            0,
          ),
        0,
      )
    },
    needsProductFdpSync(): boolean {
      return (this.cart?.cartOrders ?? []).some((order) => {
        const shippingCost = order.shippingCostResult?.shippingCost ?? 0
        const hasProductFdp = order.products.some((p) =>
          p.externalId?.startsWith(PRODUCT_FDP_PREFIX),
        )
        // FDP = 0 mais un Product FDP existe encore → à supprimer
        if (shippingCost === 0 && hasProductFdp) return true
        // FDP > 0 mais pas de Product FDP → à créer
        if (shippingCost > 0 && !hasProductFdp) return true
        return false
      })
    },
    hasAllTermsChecked(): boolean {
      const cartOrders = this.cart?.cartOrders
      if (!cartOrders || cartOrders.length === 0) return false
      for (const cartOrder of cartOrders) {
        if (!this.termsOfSales.some((e) => e === cartOrder.seller.id)) {
          return false
        }
      }
      return true
    },
    CBPaymentMethod(): AdyenPaymentMethod | undefined {
      return this.adyenPaymentMethods.find(
        (m) => m.type === AdyenPaymentMethodType.SCHEME,
      )
    },
    SEPAPaymentMethods(): AdyenPaymentMethod[] {
      return this.adyenPaymentMethods.filter(
        (m) => m.type === AdyenPaymentMethodType.BANK_TRANSFER_IBAN,
      )
    },
    hasStoredPaymentMethods(): boolean {
      return this.storedPaymentMethods?.length > 0
    },
    hasCompanyMandates(): boolean {
      return this.companyMandates?.length > 0
    },
    shippingCostTotal(): number {
      return (this.cart?.cartOrders ?? []).reduce(
        (sum: number, order) => sum + (order.shippingCostResult?.shippingCost ?? 0),
        0,
      )
    },
    shippingCostTotalWithTax(): number {
      return (this.cart?.cartOrders ?? []).reduce((sum: number, order) => {
        const fdpHT = order.shippingCostResult?.shippingCost ?? 0
        const taxRate = order.shippingCostResult?.maxTaxRate ?? 0
        return sum + fdpHT * (1 + taxRate / 100)
      }, 0)
    },
  },
})
