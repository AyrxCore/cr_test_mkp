import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Seller'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { Filter } from '@/vuejs/types/Filter'

export interface Product {
  id: number
  reference: string
  slug?: string
  name: string
  description?: string
  categories: Array<any>
  images: Array<any>
  options: Array<any>
  properties: Array<any>
  variants: []
  defaultVariantId?: number
  defaultVariantOptions: Array<any>
  priceReference: number
  percent?: number
  price?: number
  basePrice?: Price
  seller?: Seller
  accountAccordCadre: AccountAccordCadre
  isAccordCadre?: boolean
  favorites?: Array<any>
  optionVariant: Array<any>
  similarProducts: Array<any>
  selectedVariants: Array<any>
  quantity: number
}

export interface ProductFilters {
  categories?: Array<any>
  companies?: Array<any>
  properties?: Array<any>
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
}

export interface ProductStoreState {
  products: ProductCollection
  productsTopVente: ProductCollection
  productsAccordsCadre: ProductCollection
  productsSelection: ProductCollection
  cart: []
  selectedCategoryId?: string
  selectedProperties?: object
  selectedCompanyId?: string
  productVariants: []
  productVariantsOptions: []
  currentVariantOptions?: number
}

export interface SearchProductsResponse {
  filters: Array<any>
  results_count: number
  page: number
  results: Array<Product>
  parameters: any
}

export interface HomeProductsResponse {
  topVente: Product[]
  accordsCadre: Product[]
  selection: Product[]
}
