import { defineStore } from 'pinia'

import CartHttpClient from '@/vuejs/services/httpclient/CartHttpClient'
import {
  Cart,
  CartAddressesUpdate,
  CartPaymentMethodUpdated,
  CartStoreState,
  OrderItemQuantityUpdate,
  OrderShippingUpdate,
  PaymentMethod,
} from '@/vuejs/types/Cart'

import { notifyError, notifySuccess } from '@/vuejs/services/utils'

export const useCartStore = defineStore({
  id: 'cart',
  state: (): CartStoreState => ({
    cart: null,
    termsOfSales: [],
    newlyAddedProducts: [],
    modifyingCart: false,
    shippingMethods: [],
    selectedShippingMethods: {},
  }),

  actions: {
    async getCart(): Promise<void> {
      try {
        this.cart = await CartHttpClient.get().getCartAsBuyer()
        this.newlyAddedProducts = []
      } catch (error) {
        this.cart = {}
        notifyError(
          `Une erreur est survenue lors du chargement du panier, merci de contacter un administrateur.`,
        )
      }
    },
    async addProductToCart(variantId: number, quantity: number): Promise<void> {
      try {
        if (!this.cart?.id) {
          throw new Error()
        }

        const products = []
        products.push({
          variantId,
          quantity,
        })

        await CartHttpClient.get().addProductsToCartAsBuyer({
          cartId: this.cart.id,
          products,
        })

        this.productVariantsInCart.indexOf(variantId) === -1 &&
          this.newlyAddedProducts.push(variantId)

        notifySuccess('La référence du produit été ajoutée au panier')
      } catch (error) {
        notifyError(
          `L'ajout au panier est impossible, merci de contacter un administrateur.`,
        )
        throw new Error()
      }
    },
    async addProductsToCart(products): Promise<void> {
      try {
        if (!this.cart?.id) {
          throw new Error()
        }
        await CartHttpClient.get().addProductsToCartAsBuyer({
          cartId: this.cart.id,
          products,
        })
        await products.forEach((product) => {
          this.productVariantsInCart.indexOf(product.variantId) === -1 &&
            this.newlyAddedProducts.push(product.variantId)
        })
        notifySuccess(
          `Vos ${products.length} référence(s) ont été ajoutée(s) au panier avec succès`,
        )
      } catch (error) {
        notifyError(
          "L'ajout au panier est impossible, merci de contacter un administrateur.",
        )
        throw new Error()
      }
    },
    async updateProductQuantity(data: OrderItemQuantityUpdate): Promise<void> {
      try {
        await CartHttpClient.get(true).updateCartAsBuyer(data)
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors de la modification du panier, merci de contacter un administrateur.`,
        )
        throw new Error()
      }
    },
    async deleteProduct(id: number): Promise<void> {
      try {
        await CartHttpClient.get().deleteProductFromCartAsBuyer(id)
        notifySuccess('La référence du produit été retirée au panier')
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors de la modification du panier, merci de contacter un administrateur.`,
        )
        throw new Error()
      }
    },
    async updateCartAddress(data: CartAddressesUpdate): Promise<void> {
      try {
        await CartHttpClient.get(true).updateCartAdresses(data)
        this.cart.shipping_address = { id: data.shippingAddressId }
        this.cart.billing_address = { id: data.billingAddressId }
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du choix de l'adresse, merci de contacter un administrateur.`,
        )
      }
    },
    async updateOrderShipping(data: OrderShippingUpdate): Promise<void> {
      try {
        await CartHttpClient.get(true).updateOrderShipping(data)
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du choix de la méthode de livraison, merci de contacter un administrateur.`,
        )
      }
    },
    async updateCartPaymentMethod(
      paymentMethodId: number,
    ): Promise<CartPaymentMethodUpdated> {
      try {
        return await CartHttpClient.get(true).updateCartPaymentMethod({
          cartId: this.cart.id,
          paymentMethodId: paymentMethodId,
        })
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du choix de la méthode de paiement, merci de contacter un administrateur.`,
        )
      }
    },
    async findCartById(id: number): Promise<Cart> {
      try {
        return await CartHttpClient.get().findCartById(id)
      } catch (error) {
        notifyError(
          `Une erreur est survenue, merci de contacter un administrateur.`,
        )
      }
    },
    async getCartShippingMethods(cartId: number): Promise<void> {
      try {
        this.shippingMethods =
          await CartHttpClient.get().getCartShippingMethods(cartId)
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du chargement des méthodes de livraison, merci de contacter un administrateur.`,
        )
      }
    },
  },

  getters: {
    productVariantsInCart: (state): number[] => {
      let variants: number[] = []
      const orders = state.cart?.orders
      if (!orders) return state.newlyAddedProducts
      orders.forEach((o) => {
        o.items.forEach((i) => {
          variants.push(i.variant.id)
        })
      })
      return [...variants, ...state.newlyAddedProducts]
    },
    nbProducts(): number {
      return this.productVariantsInCart.length
    },
    hasAllTermsChecked(): boolean {
      const orders = this.cart?.orders
      if (!orders || orders.length === 0) return false
      for (const order of orders) {
        if (!this.termsOfSales.some((e) => e === order.seller.id)) {
          return false
        }
      }
      return true
    },
    CBPaymentMethod(): PaymentMethod {
      return this.cart?.paymentMethods?.find(
        (e) => e.name.default === 'Carte de crédit',
      )
    },
    SEPAPaymentMethod(): PaymentMethod {
      return this.cart?.paymentMethods?.find(
        (e) => e.name.default === 'Virement bancaire',
      )
    },
  },
})
