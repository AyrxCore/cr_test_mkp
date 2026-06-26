import { Seller } from '@/vuejs/types/Seller.ts'
import { Product } from '@/vuejs/types/Product.ts'

interface Currency {
  id: number
  name: string
  code: string
}

export interface CompanyMandate {
  id: number
  iban: string
  createdAt: string
}

export interface CartStoreState {
  cart?: Cart
  termsOfSales: string[]
  newlyAddedProducts: number[]
  modifyingCart: boolean
  companyMandates: Array<CompanyMandate>
  selectedSepa: AdyenPaymentMethod | null
  adyenPaymentMethods: AdyenPaymentMethod[]
  storedPaymentMethods: AdyenStoredPaymentMethod[]
  enableCreditCardStorage: boolean
  bankTransferInfo: AdyenBankTransferInfo | null
}

export interface SepaData {
  iban?: string
  bic?: string
  ownerName?: string
  phone?: string
  mandateId?: number
}

export interface CartAddressesUpdate {
  cartId: string
  shippingAddressExternalId: string
  billingAddressExternalId: string
}

export interface CartPaymentMethodUpdate {
  cartId: number
  paymentMethodType: AdyenPaymentMethodType
}

export interface CartPaymentMethodUpdated {
  payment_id: number
  payment_url: string
}

export interface CartPaymentSepaUpdate {
  cartId: number
  iban: string
  bic: string
  ownerName: string
  phone: string
  mandateId: number
}

export interface CartPaymentSepaUpdated {
  signing_url: string
}


export interface Cart {
  id: number | null
  totalPrice: number
  totalPriceWithTax: number
  currency: Currency
  productCount: number
  cartOrders: CartOrder[]
  shippingAddressExternalId: string | null
  billingAddressExternalId: string | null
}

export type ShippingRuleType = 'STANDARD' | 'STEPS' | 'FREE' | 'FIXED' | 'WEIGHT' | 'CATEGORY'

export interface ShippingCostResult {
  shippingCost: number
  remainingForFranco: number
  type: ShippingRuleType
  maxTaxRate: number
}

export interface CartOrder {
  id: number | null
  seller: Seller
  totalPrice: number
  totalPriceWithTax: number
  products: Product[]
  shippingCostResult: ShippingCostResult | null
}

export interface CartItem {
  unitPublicPrice: number
  unitPrice: number
  unitPriceWithTax: number
  quantity: number
  name: string
  sku: string
  packingType: string
  img: string
  offerPriceId: string
  options: CartItemOption[]
  variantId: string
  eco_tax?: number | null
}

export interface CartItemOption {
  id: string
  attributeName: string
  attributeValue: string
}

export enum AdyenPaymentMethodType {
  SCHEME = 'scheme',
  BANK_TRANSFER_IBAN = 'bankTransfer_IBAN',
}

export const ADYEN_PAYMENT_TYPE_LABELS: Record<AdyenPaymentMethodType, string> = {
  [AdyenPaymentMethodType.SCHEME]: 'carte bancaire',
  [AdyenPaymentMethodType.BANK_TRANSFER_IBAN]: 'virement bancaire international',
}

export interface AdyenPaymentMethod {
  name: string
  type: AdyenPaymentMethodType
  brands: string[] | null
}

export interface AdyenStoredPaymentMethod {
  id: string
  name: string
  type: AdyenPaymentMethodType | string
  brand?: string
  lastFour?: string
  expiryMonth?: string
  expiryYear?: string
  holderName?: string
}

export interface AdyenPaymentMethodsResponse {
  paymentMethods: AdyenPaymentMethod[]
  storedPaymentMethods: AdyenStoredPaymentMethod[]
  enableCreditCardStorage: boolean
}

export enum AdyenResultCode {
  AUTHORISED = 'Authorised',
  PENDING = 'Pending',
  REFUSED = 'Refused',
  CANCELLED = 'Cancelled',
  ERROR = 'Error',
  RECEIVED = 'Received',
}

export interface AdyenBankTransferInfo {
  beneficiary: string
  iban: string
  bic: string
  reference: string
  totalAmount: string
}

export interface AdyenInitiatePaymentPayload {
  browserInfo: {
    acceptHeader: string
    colorDepth: number
    javaEnabled: boolean
    javaScriptEnabled: boolean
    language: string
    screenHeight: number
    screenWidth: number
    timeZoneOffset: number
    userAgent: string
  }
  customerUserIP: string
  paymentMethodData: Record<string, unknown>
  reference: string
  storePaymentMethod: boolean
  returnPath: string
  countryCode: string
  amount: {
    value: number
    currency: string
  }
}

export interface AdyenInitiatePaymentResponse {
  resultCode: AdyenResultCode | string
  pspReference?: string
  action?: {
    type: string
    url?: string
    method?: string
    token?: string
    beneficiary?: string
    iban?: string
    bic?: string
    totalAmountValue?: string
    [key: string]: unknown
  }
}

export interface AdyenSubmitDetailsPayload {
  /** Données de détails retournées par le SDK Adyen (threeDSResult, redirectResult, MD/PaRes…) */
  details: Record<string, unknown>
  /** Requis par l'API Adyen pour les challenges 3DS2 natifs (absent pour les redirections 3DS1) */
  paymentData?: string
}

/** Détails additionnels retournés par le SDK Adyen (3DS, redirect…) */
export type AdyenAdditionalDetails = Record<string, unknown>

