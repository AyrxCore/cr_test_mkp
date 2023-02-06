import { Property } from '@/vuejs/types/Product/Property'
import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Product/Seller'

export interface Product {
  reference: string
  name: string
  description?: string
  conditionnement?: string
  livraisons: Array<any>
  categories: Array<any>
  images: Array<any>
  options: Array<any>
  properties: Array<Property>
  variants: []
  priceReference: number
  percent?: number
  price?: Price
  basePrice?: Price
  company?: Seller
}
