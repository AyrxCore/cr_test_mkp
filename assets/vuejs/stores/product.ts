import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import {
  Product,
  ProductFilters,
  ProductStoreState,
} from '@/vuejs/types/Product'
import { arrayEqual } from '@/vuejs/services/utils'
import {
  HOME_ACCORD_CADRE_PROPERTY,
  HOME_SELECTION_PROPERTY,
  HOME_TOP_VENTE_PROPERTY,
} from '@/vuejs/services/const'

export const useProductStore = defineStore({
  id: 'product',
  state: (): ProductStoreState => ({
    products: {
      filters: {},
      parameters: {},
      results: [],
      page: 1,
      resultsCount: 0,
    },
    productsTopVente: null,
    productsAccordsCadre: null,
    productsSelection: null,
    cart: [],
    selectedCategoryId: null,
    selectedProperties: null,
    selectedCompanyId: null,
    productVariants: [],
    productVariantsOptions: [],
    currentVariantOptions: null,
  }),

  actions: {
    async fetchProductsByParams(params): Promise<void> {
      try {
        this.products = await ProductHttpClient.get().fetchProductsByParams(
          params,
        )
        return this.products
      } catch (error) {}
    },
    async initHomeProducts() {
      try {
        this.productsTopVente =
          await ProductHttpClient.get().fetchProductsByParams(
            HOME_TOP_VENTE_PROPERTY,
          )
        this.productsAccordsCadre =
          await ProductHttpClient.get().fetchProductsByParams(
            HOME_ACCORD_CADRE_PROPERTY,
          )
        this.productsSelection =
          await ProductHttpClient.get().fetchProductsByParams(
            HOME_SELECTION_PROPERTY,
          )
      } catch (error) {}
    },
    async initProduct(id: number) {
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
    async changeVariant(product: Product, optionsSelected: Array<number>) {
      let variantSelected = null
      Object.entries(product.variants).find(([key, value], index) => {
        if (arrayEqual(value.options, optionsSelected)) {
          variantSelected = value.id
        }
      })

      if (variantSelected) {
        product.defaultVariantId = parseInt(variantSelected)
        let variant = await this.productVariants.find((v) => {
          if (v.id === product.defaultVariantId) {
            return v
          }
          return null
        })
        if (!variant) {
          variant = await this.findVariantById(product.defaultVariantId)
          this.productVariants.push(variant)
        }

        product = this.updateProductVariant(product, variant)
      } else {
        product.defaultVariantId = null
      }

      return product
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
    async findSimilarProducts(categoryId: number, productId: number) {
      const alertStore = useAlertStore()
      try {
        const similarProducts = await this.fetchProductsByParams({
          perPage: 20,
          categories: categoryId,
        })

        return similarProducts.results.filter(
          (sp) => sp.id !== productId && !sp.isAccordCadre,
        )
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async findPartnerProducts(partnerId: number) {
      const alertStore = useAlertStore()
      try {
        const partnerProducts = await this.fetchProductsByParams({
          perPage: 8,
          companies: partnerId,
        })

        return partnerProducts.results.filter((sp) => !sp.isAccordCadre)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async findDefaultVariantProduct(product: Product) {
      const alertStore = useAlertStore()
      try {
        const variant = await this.findVariantById(product.defaultVariantId)
        this.productVariants.push(variant)
        this.updateProductVariant(product, variant)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    clearFilters() {
      this.selectedCategoryId = null
      this.selectedProperties = null
      this.selectedCompanyId = null
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
    updateProductVariant(product: Product, variant) {
      product.defaultVariantId = variant.id
      product.price = variant.price?.display_price / 100
      const priceDiff = product.priceReference - product.price
      product.percent = Math.round((priceDiff * 100) / product.priceReference)

      return product
    },
  },
  getters: {
    filters(): ProductFilters {
      return this.products.filters
    },
    hasFilters(): boolean {
      return (
        !!this.selectedCategoryId ||
        !!this.selectedProperties ||
        !!this.selectedCompanyId
      )
    },
  },
})
