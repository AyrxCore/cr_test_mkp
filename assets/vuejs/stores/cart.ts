import { defineStore } from 'pinia'

import CartHttpClient from '@/vuejs/services/httpclient/CartHttpClient'
import {
  CartAddressesUpdate,
  CartStoreState,
  Order,
  OrderItemQuantityUpdate,
  OrderShippingUpdate,
  PaymentMethod,
  CartPaymentMethodUpdated,
  Cart,
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
        await CartHttpClient.get().addProductToCartAsBuyer({
          cartId: this.cart.id,
          variantId,
          quantity,
        })

        notifySuccess('Le produit a été ajouté au panier')
        this.productVariantsInCart.indexOf(variantId) === -1 &&
          this.newlyAddedProducts.push(variantId)
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
          products: products,
        })
        await products.forEach((product) => {
          this.productVariantsInCart.indexOf(product.variantId) === -1 &&
            this.newlyAddedProducts.push(product.variantId)
        })
        notifySuccess(
          `Vos ${products.length} produit(s) ont été ajouté(s) au panier avec succès`,
        )
      } catch (error) {
        notifyError(
          `L'ajout au panier est impossible, merci de contacter un administrateur.`,
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
      return this.termsOfSales.length === this.cart?.orders?.length
    },
    hasAllShippingMethodsSelected(): boolean {
      let hasAll = true
      this.cart?.orders?.forEach((e: Order) => {
        if (e.shipments.length === 0) hasAll = false
      })
      return hasAll
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
