import { OrderItem } from '@/vuejs/types/Cart'

export interface Order {
  id: number
  orderNumber: string
  items: Array<OrderItem>
  total: number
  totalExcludingTaxes: number
  state: string
  billingAddress: string
  shippingAddress: string
  shippingState: string
  shipmentAmount: number
  paymentId?: number
  createdAt: Date
  updatedAt?: Date
  confirmedAt?: Date
  shippedAt: Date
  deliveredAt?: Date
  canceledAt?: Date
  refusedAt?: Date
}

export interface OrderStoreState {
  orders: Order[]
}

export interface Invoice {
  id: number
  name: string
  content: string
}
