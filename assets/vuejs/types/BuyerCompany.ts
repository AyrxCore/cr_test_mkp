import { Address } from '@/vuejs/types/Address'

export interface BuyerCompanyStoreState {
  addresses: Address[]
  currentAddress: Address
  isloading: boolean
}
