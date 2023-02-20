import { Address } from '@/vuejs/types/Address'

export interface BuyerCompanyStoreState {
  adresses: Address[]
  currentAddress: Address
  isloading: boolean
}
