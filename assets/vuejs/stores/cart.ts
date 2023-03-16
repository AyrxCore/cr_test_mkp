import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import CartHttpClient from '@/vuejs/services/httpclient/CartHttpClient'
import {
  CartAddressesUpdate,
  CartStoreState,
  Order,
  OrderItemQuantityUpdate,
  OrderShippingUpdate,
  PaymentMethod,
  CartPaymentMethodUpdated,
} from '@/vuejs/types/Cart'

export const useCartStore = defineStore({
  id: 'cart',
  state: (): CartStoreState => ({
    cart: null,
    termsOfSales: [],
  }),

  actions: {
    async getCart(): Promise<void> {
      const alertStore = useAlertStore()
      try {
        this.cart = await CartHttpClient.get().getCartAsBuyer()
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async addProductToCart(variantId: number, quantity: number): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await CartHttpClient.get().addProductToCartAsBuyer({
          cartId: this.cart.id,
          variantId,
          quantity,
        })
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async updateProductQuantity(data: OrderItemQuantityUpdate): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await CartHttpClient.get(true).updateCartAsBuyer(data)
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async deleteProduct(id: number): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await CartHttpClient.get().deleteProductFromCartAsBuyer(id)
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async updateCartAddress(data: CartAddressesUpdate): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await CartHttpClient.get(true).updateCartAdresses(data)
        this.cart.shipping_address = { id: data.shippingAddressId }
        this.cart.billing_address = { id: data.billingAddressId }
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async updateOrderShipping(data: OrderShippingUpdate): Promise<void> {
      const alertStore = useAlertStore()
      try {
        await CartHttpClient.get(true).updateOrderShipping(data)
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async updateCartPaymentMethod(
      paymentMethodId: number,
    ): Promise<CartPaymentMethodUpdated> {
      const alertStore = useAlertStore()
      try {
        return await CartHttpClient.get(true).updateCartPaymentMethod({
          cartId: this.cart.id,
          paymentMethodId: paymentMethodId,
        })
      } catch (error) {
        console.log(error)
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
  },

  getters: {
    nbProducts(): number {
      let nbProducts = 0
      this.cart?.orders?.forEach((order: Order) => {
        nbProducts += order.items.length
      })
      return nbProducts
    },
    hasAllTermsChecked(): boolean {
      return this.termsOfSales.length === this.cart?.orders.length
    },
    hasAllShippingMethodsSelected(): boolean {
      return this.cart?.orders.forEach((e: Order) => {
        if (e.shipments.length === 0) return false
      })
    },
    CBPaymentMethod(): PaymentMethod {
      return this.cart?.paymentMethods.find(
        (e) => e.name.default === 'Carte de crédit',
      )
    },
    SEPAPaymentMethod(): PaymentMethod {
      return this.cart?.paymentMethods.find(
        (e) => e.name.default === 'Virement bancaire',
      )
    },
  },
})
