import type { OptionValue } from '@/vuejs/modules/products/utils/option-utils'

export interface Variant {
  id: string
  externalId: string
  offerPriceExternalId?: string | null
  options: Record<string, OptionValue>
  price?: number | null
  priceReference?: number | null
  percent: number
  images: string[]
}

