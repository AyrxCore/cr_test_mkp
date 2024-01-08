import { Property } from '@/vuejs/types/Product/Property'
import { Seller } from '@/vuejs/types/Seller'

export interface Filter {
  companies: Array<Seller>
  properties: Array<Property>
}
