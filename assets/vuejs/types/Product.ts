import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Seller'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'

export interface Product {
  id: number
  reference: string
  slug?: string
  name: string
  description?: string
  conditionnement?: string
  livraisons: Array<any>
  categories: Array<any>
  images: Array<any>
  options: Array<any>
  properties: Array<any>
  variants: []
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
  selectedVariantId: number
  selectedVariants: Array<any>
  quantity: number
}
