import { CartItem } from '@/vuejs/types/Cart'

export interface Order {
  id: number
  orderNumber: string
  items: Array<CartItem>
  productCount: number
  total: number
  totalExcludingTaxes: number
  state: string
  billingAddress: string
  shippingAddress: string
  shippingState: string
  shippingTrackingUrl?: string
  shipmentAmount: number
  invoiceUrl?: string
  orderInvoiceLinks?: Array<{ reference: string; invoiceUrl: string | null }>
  orderPartners?: Array<{
    reference: string
    partnerName: string
    state: string
    shippingState: string
    shippingTrackingUrl: string | null
    invoiceUrl: string | null
    items: CartItem[]
  }>
  createdAt: Date
  updatedAt?: Date
  confirmedAt?: Date
  shippedAt?: Date // Badge "Expédiée" (OrderComponent)
  deliveredAt?: Date // Badge "Livrée" (OrderComponent)
}

export interface OrderStoreState {
  orders: Order[]
}
