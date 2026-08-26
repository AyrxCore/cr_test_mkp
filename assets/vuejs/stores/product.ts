import { defineStore, storeToRefs } from 'pinia'

import { useAlertStore } from '@/vuejs/stores/alert'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useSellerStore } from '@/vuejs/stores/seller'

import { getErrorMessage } from '@/vuejs/services/login'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'

import { AlertType } from '@/vuejs/types/Alert'
import { HttpStatusCodes } from '@/vuejs/types/HttpClient'
import {
  Product,
  ProductStoreState,
  DjustProductType,
} from '@/vuejs/types/Product'
import { arrayEqual, hexToBinary, notifyError } from '@/vuejs/services/utils'
import { Seller } from '@/vuejs/types/Seller'
import { Category } from '@/vuejs/types/Product/Category'

export const useProductStore = defineStore('product', {
  state: (): ProductStoreState => ({
    products: {
      filters: {},
      parameters: {},
      results: [],
      page: 1,
      resultsCount: 0,
    },
    productsAccordsCadre: null,
    productsSelection: null,
    cart: [],
    selectedCategoryId: null,
    selectedProperties: null,
    selectedSellerId: null,
    searchTerms: null,
    productVariants: [],
    productVariantsOptions: [],
    currentVariantOptions: null,
  }),

  actions: {
    async fetchProductsByParams(params): Promise<void> {
      const formattedParams = { ...params }

      if (this.selectedCategoryId && this.selectedSearchCategory) {
        formattedParams.categories = this.selectedSearchCategory.externalId
      }
      if (this.selectedSellerId) {
        formattedParams.sellers = [this.selectedSearchSeller?.externalId ?? this.selectedSellerId]
      }
      if (this.selectedProperties) {
        formattedParams.properties = this.selectedProperties
      }
      try {
        this.products =
          await ProductHttpClient.get().fetchProductsByParams(formattedParams)
        return this.products
      } catch (error) {
        notifyError(
          'Une erreur est survenue lors de la récupération des produits.',
        )
        throw error
      }
    },
    async initSliderAccordsCadres() {
      try {
        const { channelSliderAccordsCadresProperty } =
          storeToRefs(useChannelStore())

        if (!channelSliderAccordsCadresProperty.value) {
          this.productsAccordsCadre = null
          return
        }

        this.productsAccordsCadre =
          await ProductHttpClient.get().fetchProductsByParams({
            productTags: channelSliderAccordsCadresProperty.value,
          })
      } catch (_error) {
        // Ignored: product slider is optional
      }
    },
    async initSliderProductsSelection() {
      try {
        const { channelSliderProductsSelectionProperty } =
          storeToRefs(useChannelStore())

        if (!channelSliderProductsSelectionProperty.value) {
          this.productsSelection = null
          return
        }

        this.productsSelection =
          await ProductHttpClient.get().fetchProductsByParams({
            productTags: channelSliderProductsSelectionProperty.value,
          })
      } catch (_error) {
        // Ignored: product slider is optional
      }
    },
    async initProduct(id: number | string) {
      const alertStore = useAlertStore()
      try {
        return await ProductHttpClient.get().findProductById(id)
      } catch (error) {
        if (error.response.status === HttpStatusCodes.unauthorized) {
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
    },
    async changeVariant(product: Product, optionsSelected: Array<number>) {
      let variantSelected = null
      Object.entries(product.variants).find(([_key, value], _index) => {
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
        // Le variant par défaut est maintenant dans product.variants
        // Plus besoin d'appel API séparé

        if (variant) {
          product = this.updateProductVariant(product, variant)
        }
      } else {
        product.defaultVariantId = null
      }

      return product
    },
    async findAccordCadreById(id) {
      const alertStore = useAlertStore()
      try {
        return await ProductHttpClient.get().findProductById(id)
      } catch (error) {
        if (error.response.status === HttpStatusCodes.unauthorized) {
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
    },
    async findSimilarProducts(categoryId: number) {
      const alertStore = useAlertStore()
      try {
        return await this.fetchProductsByParams({
          perPage: 20,
          categories: categoryId,
        })
      } catch (error) {
        if (error.response.status === HttpStatusCodes.unauthorized) {
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
    },
    async findPartnerProducts(partnerId: number | string) {
      const alertStore = useAlertStore()
      try {
        const partnerProducts = await this.fetchProductsByParams({
          perPage: 8,
          sellers: partnerId,
        })

        return partnerProducts.results.filter((sp) => !this.isAccordCadre(sp))
      } catch (error) {
        if (error.response.status === HttpStatusCodes.unauthorized) {
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
    },
    async findDefaultVariantProduct(product: Product) {
      // Les variants sont maintenant inclus dans les données du produit Djust
      // Plus besoin d'appel API séparé
      const alertStore = useAlertStore()
      try {
        const variant = product.variants?.find(
          (v) => v.id === product.defaultVariantId,
        )
        if (variant) {
          this.productVariants.push(variant)
          this.updateProductVariant(product, variant)
        }
      } catch (error) {
        if (error.response.status === HttpStatusCodes.unauthorized) {
          alertStore.setShow(
            getErrorMessage(error.response.data.message),
            AlertType.danger,
          )
        }
      }
    },
    clearFilters() {
      this.selectedCategoryId = null
      this.selectedProperties = null
      this.selectedSellerId = null
    },
    async downloadPdfFile(url: string) {
      try {
        const file = await ProductHttpClient.get().downloadPdfFile<{
          content: string
          name: string
        }>(url)
        const fileContentBinary = hexToBinary(file.content)

        const blob = new Blob([fileContentBinary], { type: 'application/pdf' })
        const link = document.createElement('a')
        link.href = URL.createObjectURL(blob)
        link.download = file.name
        link.click()
      } catch (error) {
        notifyError(error.response.data.message)
      }
    },
    setSelectedCategory(categoryId) {
      this.selectedCategoryId = categoryId
    },
    setSelectedProperty(property) {
      this.selectedProperties = property
    },
    setSelectedSeller(sellerId) {
      this.selectedSellerId = sellerId
    },
    setSearchTerms(searchterms) {
      this.searchTerms = searchterms
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
    hasFilters(): boolean {
      return (
        !!this.selectedCategoryId ||
        !!this.selectedProperties ||
        !!this.selectedSellerId
      )
    },
    selectedSearchCategory(): Category | undefined {
      const categoryStore = useCategoryStore()

      const findCategoryById = (
        categories: Array<Category>,
        id: string,
      ): Category | undefined => {
        for (const category of categories) {
          if (category.id === id) {
            return category
          }
          if (category.children && category.children.length > 0) {
            const found = findCategoryById(category.children, id)
            if (found) {
              return found
            }
          }
        }
        return undefined
      }

      return findCategoryById(categoryStore.categories, this.selectedCategoryId)
    },
    selectedSearchSeller(): Seller | undefined {
      const sellerStore = useSellerStore()
      const key = this.selectedSellerId
      if (!key) {
        return undefined
      }
      return sellerStore.allSellers.find(
        (seller) => seller.externalId === key,
      )
    },
    isSellable:
      () =>
      (product: Product): boolean => {
        return product.productType === DjustProductType.SELLABLE
      },
    isNotSellable:
      () =>
      (product: Product): boolean => {
        return product.productType === DjustProductType.NOT_SELLABLE
      },
    isAccordCadre:
      () =>
      (product: Product): boolean => {
        return product.productType === DjustProductType.ACCORD_CADRE
      },
  },
})
