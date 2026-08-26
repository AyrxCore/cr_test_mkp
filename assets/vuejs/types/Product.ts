import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Seller'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { Category } from '@/vuejs/types/Product/Category'
import { AccordCadreContent } from '@/vuejs/types/AccordCadre.ts'
import { Variant } from '@/vuejs/types/Variant'
import { OptionValue } from '@/vuejs/modules/products/utils/option-utils'

export enum DjustProductType {
  SELLABLE = 'SELLABLE',
  NOT_SELLABLE = 'NOT_SELLABLE',
  ACCORD_CADRE = 'ACCORD_CADRE',
}

export interface Product {
  id: string
  externalId?: string
  reference: string
  slug?: string
  name: string
  description?: string
  categories: Array<Category>
  images: string[]
  options: Record<string, ProductOptionData>
  properties: ProductProperties
  tags: string[]
  variants?: Variant[]
  defaultVariantId: string
  priceReference: number
  percent?: number
  price?: number
  basePrice?: Price
  seller?: Seller
  accountAccordCadre?: AccountAccordCadre
  notSellableFormWithMessage?: boolean
  productType: DjustProductType
  tarifId?: string
  productTopLabel?: string
  productPricingPhrase?: string
  formWithMessageFat?: boolean
  favorites?: Array<Record<string, unknown>>
  optionVariant: Array<Record<string, unknown>>
  similarProducts: Array<Record<string, unknown>>
  selectedVariants: Array<Record<string, unknown>>
  quantity: number
  newTarifNotification?: boolean
  // Nouveaux champs pour Djust
  minOrderQuantity?: number
  maxOrderQuantity?: number
  attachments?: Array<ProductAttachment>
  accordCadreContent?: AccordCadreContent
  accordId?: string
  sku: string
  shippingCategory?: string
  ecoTax?: number | null
  offerPriceExternalId?: string | null
}

export interface ProductAttachment {
  name: string
  url: string
  type?: string
}

export interface ProductOptionData {
  type?: string
  values: OptionValue[]
}

export interface ProductProperties {
  [key: string]: string
}

export interface ProductFilters {
  categories?: Category[]
  sellers?: Array<Record<string, unknown>>
  properties?: Array<Record<string, unknown>>
}

export interface ProductParameters extends ProductFilters {
  name?: string
}

export interface ProductCollection {
  filters: ProductFilters
  parameters: ProductParameters
  results: Array<Product>
  page: number
  resultsCount: number
  accordCadres?: Array<Product>
  accordCadresCount?: number
}

export interface ProductStoreState {
  products: ProductCollection
  productsAccordsCadre: ProductCollection
  productsSelection: ProductCollection
  cart: []
  selectedCategoryId?: string
  selectedProperties?: object
  selectedSellerId?: string
  searchTerms?: string
  productVariants: []
  productVariantsOptions: []
  currentVariantOptions?: number
}
