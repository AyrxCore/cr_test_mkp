
import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { Product } from '@/vuejs/types/Product'
import { Filter } from '@/vuejs/types/Filter'

export interface ProductStoreState {
  products: Product[],
  filters: Filter[],
  productsSelection: Product[],
  cart: [],
}

export const useProductStore = defineStore({
  id: 'product',
  state: (): ProductStoreState => ({
    products: [],
    filters: [],
    productsSelection: [],
    cart: []
  }),

  actions: {
    async fetchProductsByParams(params): Promise<[]> {
      try {
        return  await ProductHttpClient.get().fetchProductsByParams(params)
      } catch (error) {
        return []
      }
    },
    async getHomeProducts(type) {
      try {
          return await ProductHttpClient.get().fetchHomeProducts(type)
      } catch (error) {
        console.log(error)
      }
    },
    async fetchProductsWithFilterByParams(params) {
      try {
          return  await this.fetchProductsByParams(params)
      } catch (error) {
        console.log(error)

      }
    },
    async findProductById(id) {
      const alertStore = useAlertStore()
      try {
        return await ProductHttpClient.get().findProductById(id)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }
    },
    async findAccordCadreById(id) {
      const alertStore = useAlertStore()
      try {
        return await ProductHttpClient.get().findAccordCadreById(id)
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
