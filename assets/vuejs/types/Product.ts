import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Seller'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { Category } from '@/vuejs/types/Product/Category'

export interface Product {
  id: number
  reference: string
  slug?: string
  name: string
  description?: string
  categories: Array<Category>
  images: Array<any>
  options: Array<any>
  properties: ProductProperties
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
  sellable?: boolean
  notSellableFormWithMessage?: boolean
  formWithMessageFat?: boolean
  favorites?: Array<any>
  optionVariant: Array<any>
  similarProducts: Array<any>
  selectedVariants: Array<any>
  quantity: number
  newTarifNotification?: boolean
}

export interface ProductProperties {
  [key: string]: string
}

export interface ProductCategory {
  id: number
  checked: boolean
  name: string
  parentId: number
  children: ProductCategory[]
  image: string
  productCount: number
}

export interface ProductFilters {
  categories?: ProductCategory[]
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
  productsAccordsCadre: ProductCollection
  productsSelection: ProductCollection
  cart: []
  selectedCategoryId?: string
  selectedProperties?: object
  selectedCompanyId?: string
  searchTerms?: string
  productVariants: []
  productVariantsOptions: []
  currentVariantOptions?: number
}
