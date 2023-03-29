import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { Product } from '@/vuejs/types/Product'
import { Filter } from '@/vuejs/types/Filter'

export interface ProductStoreState {
  products: Product[]
  filters: Filter[]
  productsTopVente: Product[]
  productsAccordsCadre: Product[]
  productsSelection: Product[]
  cart: []
  selectedCategoryId?: string
  selectedProperties?: object
  selectedCompanyId?: string
}

export const useProductStore = defineStore({
  id: 'product',
  state: (): ProductStoreState => ({
    products: [],
    filters: [],
    productsTopVente: [],
    productsAccordsCadre: [],
    productsSelection: [],
    cart: [],
    selectedCategoryId: null,
    selectedProperties: null,
    selectedCompanyId: null,
  }),

  actions: {
    async fetchProductsByParams(params): Promise<[]> {
      try {
        return await ProductHttpClient.get().fetchProductsByParams(params)
      } catch (error) {
        return []
      }
    },
    async initHomeProducts() {
      try {
        const products = await ProductHttpClient.get().fetchHomeProducts()
        this.productsTopVente = products.topVente
        this.productsAccordsCadre = products.accordsCadre
        this.productsSelection = products.selection
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
    async findVariantById(id) {
      const alertStore = useAlertStore()
      try {
        return await ProductHttpClient.get().findVariantById(id)
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
    setSelectedCategory(categoryId) {
      this.selectedCategoryId = categoryId
    },
    setSelectedProperty(property) {
      this.selectedProperties = property
    },
    setSelectedCompany(companyId) {
      this.selectedCompanyId = companyId
    },

  },
})
