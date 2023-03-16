import { Address } from '@/vuejs/types/Address'

interface EntityId {
  id: number
}

interface Currency {
  id: number
  name: string
  code: string
}

interface Seller {
  id: number
  name: string
}

export interface CartStoreState {
  cart?: Cart
  termsOfSales: number[]
  newlyAddedProduct: number
}

interface LanguageVariation {
  default: string
  fr?: string
}

export interface OrderProduct {
  id: number
  description: LanguageVariation
  name: LanguageVariation
  reference: number | string
  price_reference: number
}

export interface OrderItemVariant {
  id: number
  product: OrderProduct
}

export interface OrderItem {
  id: number
  quantity: number
  total: number
  total_excluding_taxes: number
  variant: OrderItemVariant
  canceled_at: null
}

export interface AddOrderItem {
  cartId: number
  variantId: number
  quantity: number
}

export interface OrderItemQuantityUpdate {
  id: number
  quantity: number
}

export interface OrderShippingUpdate {
  cartId: number
  orderId: number
  shippingId: number
}

export interface CartAddressesUpdate {
  cartId: number
  shippingAddressId: number
  billingAddressId: number
}

export interface CartPaymentMethodUpdate {
  cartId: number
  paymentMethodId: number
}

export interface CartPaymentMethodUpdated {
  payment_id: number
  payment_url: string
}

export interface ShippingMethod {
  amount: number
  selected: boolean
  shipping_method: {
    id: number
    name: {
      fr: string
      default: string
    }
  }
}

export interface PaymentMethod {
  id: number
  name: {
    fr: string
    default: string
  }
}

export interface Order {
  id: number | null
  type: string
  state: string
  buyer: EntityId
  buyer_user: EntityId
  seller: Seller
  seller_user: EntityId
  items: OrderItem[]
  promotion: null
  taxes: []
  items_total_excluding_taxes: number
  items_total: number
  total_excluding_taxes: number
  total: number
  currency: Currency
  note: string | null
  shippingMethodsAvailable: ShippingMethod[]
  shipments: EntityId[]
}

export interface Cart {
  id: number | null
  type: string
  state: string
  buyer: EntityId
  user: EntityId
  total: number
  total_excluding_taxes: number
  orders: Order[]
  shipping_address: Address | null
  billing_address: Address | null
  currency: Currency
  paymentMethods: PaymentMethod[]
  payment_method: null
  created_at: string
  updated_at: string
  origin_cart_id: null
}
