import { Property } from '@/vuejs/types/Product/Property'
import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Seller'
import { AccountAccordCadre } from '@/vuejs/types/AccordCadre';

export interface Product {
  reference: string
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
  price?: Price
  basePrice?: Price
  seller?: Seller

  accountAccordCadre: AccountAccordCadre

  isAccordCadre?: boolean
}
