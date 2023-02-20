import { Property } from '@/vuejs/types/Product/Property'
import { Price } from '@/vuejs/types/Product/Price'
import { Seller } from '@/vuejs/types/Seller'
import { Country } from '@/vuejs/types/Country';

export interface Filter {
  companies: Array<Seller>
  properties: Array<Property>
}
