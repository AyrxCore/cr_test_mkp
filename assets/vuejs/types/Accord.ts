import { StoreData } from '@/vuejs/types/Seller'

export interface AccordApiResponse {
  id: string
  name: string
  logo: string
  stores: StoreData[]
}
