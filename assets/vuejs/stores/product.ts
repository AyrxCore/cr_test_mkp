
import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { Product } from '@/vuejs/types/Product'
import { Address } from '@/vuejs/types/Address';

export interface ProductStoreState {
  products: Product[],
  productsTopVente: Product[],
  productsSelection: Product[],
  cart: [],
}

export const useProductStore = defineStore({
  id: 'product',
  state: (): ProductStoreState => ({
    products: [],
    productsTopVente: [],
    productsSelection: [],
    cart: []
  }),

  actions: {
    async getProductsByParams(params): Promise<[]> {
      try {
        return  await ProductHttpClient.get().fetchProductsByParams(params)
      } catch (error) {
        return []
      }
    },
    async getProductsTopVente(params) {
      try {
          return await this.getProductsByParams(params)
      } catch (error) {
        console.log(error)

      }
    },
    async getProductsSelection(params) {
      try {
          return  await this.getProductsByParams(params)
      } catch (error) {
        console.log(error)

      }
    },
    async getProductById(id) {
      const alertStore = useAlertStore()
      try {
        return await ProductHttpClient.get().getProduct(id)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }
    },
  },
})
