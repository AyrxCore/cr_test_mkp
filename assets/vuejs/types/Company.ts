import { Address } from '@/vuejs/types/Address'

export interface CompanyStoreState {
  adresses: Address[]
  currentAddress: Address
  isloading: boolean
}
