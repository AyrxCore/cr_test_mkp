import { defineStore } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { Product } from '@/vuejs/types/Product'
import { Filter } from '@/vuejs/types/Filter'
import { arrayEqual } from '@/vuejs/services/utils'
import { th } from 'date-fns/locale'

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
      } catch (error) {}
    },
    async findProductById(id) {
      const alertStore = useAlertStore()
      try {
        const product = await ProductHttpClient.get().findProductById(id)
        product.optionVariant = Object.values(
          Object.values(product.variants)[0],
        )
        product.quantity = 1
        await this.findDefaultVariantProduct(product)

        return product
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
      }
    },
    async changeVariant(product: Product) {
      let variantSelected = null
      Object.entries(product.variants).find(([key, value], index) => {
        if (arrayEqual(value, product.optionVariant)) {
          variantSelected = key
        }
      })

      if (variantSelected) {
        product.selectedVariantId = parseInt(variantSelected)
        let variant = await product.selectedVariants.find((v) => {
          if (v.id === product.selectedVariantId) {
            return v
          }
          return null
        })
        if (!variant) {
          variant = await this.findVariantById(product.selectedVariantId)
          product.selectedVariants.push(variant)
        }

        this.updateProductPrice(product, variant)
      } else {
        product.selectedVariantId = null
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
    async findSimilarProducts(categoryId: number, productId: number) {
      const alertStore = useAlertStore()
      try {
        const similarProducts = await this.fetchProductsByParams({
          perPage: 5,
          categories: [categoryId],
        })

        return similarProducts.filter(
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
    async findDefaultVariantProduct(product: Product) {
      const alertStore = useAlertStore()
      try {
        product.selectedVariantId = parseInt(Object.keys(product.variants)[0])
        if (Object.keys(product.variants).length > 2) {
          const variant = await this.findVariantById(product.selectedVariantId)
          product.selectedVariants = []
          product.selectedVariants.push(variant)
          this.updateProductPrice(product, variant)
        }
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
    updateProductPrice(product, variant) {
      product.price = variant.price?.display_price / 100
      const priceDiff = product.priceReference - product.price
      product.percent = Math.round((priceDiff * 100) / product.priceReference)
    },
  },
})
