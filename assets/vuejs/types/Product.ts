import { Category } from '@/vuejs/types/Product/Category'
import { Property } from '@/vuejs/types/Product/Property'
import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Product/Seller'

export interface Product {
  reference: string
  name: string
  description?: string
  categories: Category[]
  images: []
  options: []
  properties: Property[]
  variants: []
  priceReference: number
  price?: Price
  basePrice?: Price
  company?: Seller
}
