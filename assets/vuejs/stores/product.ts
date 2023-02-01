
import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { Product } from '@/vuejs/types/Product'

export interface ProductStoreState {
  products: Product[],
  productsTopVente: Product[],
  productsSelection: Product[],
  cart: [],
  currentProductId: number
}

export const useProductStore = defineStore({
  id: 'product',
  state: (): ProductStoreState => ({
    products: [],
    productsTopVente: [],
    productsSelection: [],
    currentProductId: null,
    cart: []
  }),

  actions: {
    async findProductsByParams(params): Promise<[]> {
      try {
        return  await ProductHttpClient.get().findProductsByParams(params)
      } catch (error) {
        return []
      }
    },
    async findProductsTopVente(params): Promise<void> {
      try {
        if (this.productsTopVente.length === 0) {
          this.productsTopVente = await this.findProductsByParams(params)
        }
      } catch (error) {
        console.log(error)

      }
    },
    async findProductsSelection(params): Promise<void> {
      try {
        if (this.productsSelection.length === 0) {
          this.productsSelection = await this.findProductsByParams(params)
        }
      } catch (error) {
        console.log(error)

      }
    },

    async getProduct() {
      const alertStore = useAlertStore()
      if (this.currentProductId) {
        try {
          return await ProductHttpClient.get().getProduct(this.currentProductId)
        } catch (error) {
          error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
      return null

    },
  },

  getters: {
    getProducts() :Array<Product>{
      return this.products
    },
    getProductsTopVente() :Array<Product>{
      return this.productsTopVente
    },
    getProductsSelection() :Array<Product>{
      return this.productsSelection
    },
  },
})
