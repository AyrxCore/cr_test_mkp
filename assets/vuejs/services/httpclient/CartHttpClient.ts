import BaseClientService from '@/vuejs/services/BaseClientService'

import {
  AdyenInitiatePaymentPayload,
  AdyenInitiatePaymentResponse,
  AdyenPaymentMethodsResponse,
  AdyenSubmitDetailsPayload,
  Cart,
  CartAddressesUpdate,
  CartPaymentMethodUpdate,
  CartPaymentMethodUpdated,
  CartPaymentSepaUpdate,
  CartPaymentSepaUpdated,
} from '@/vuejs/types/Cart'

export default class CartHttpClient extends BaseClientService {
  public getCart<T extends Cart>(): Promise<T> {
    return this.apiClient.get<T>('cart').then((response) => response.data)
  }

  public syncCart(cartId: string | number): Promise<{ removedOfferPriceIds: string[] }> {
    return this.apiClient
      .post<{ removedOfferPriceIds: string[] }>(`carts/${cartId}/sync`, {})
      .then((response) => response.data)
  }

  public updateProductsToCart(cartId: string | number, data: unknown[]): Promise<void> {
    return this.apiClient
      .put(`carts/${cartId}/lines`, data)
      .then((response) => response.data)
  }

  public deleteCartLines(cartId: string | number, offerPriceIds: string[]): Promise<void> {
    return this.apiClient
      .delete(`carts/${cartId}/lines`, { data: { offerPriceIds } })
      .then((response) => response.data)
  }

  public syncProductsFdp(cartId: string): Promise<void> {
    return this.apiClient
      .put(`carts/${cartId}/product_fdp`, {})
      .then((response) => response.data)
  }

  public updateCartAddresses<T extends []>({
    cartId,
    shippingAddressExternalId,
    billingAddressExternalId,
  }: CartAddressesUpdate): Promise<T> {
    return this.apiClient
      .patch(`cart_addresses/${cartId}`, {
        shippingAddressExternalId,
        billingAddressExternalId,
      })
      .then((response) => response.data)
  }

  public updateCartPaymentMethod<T extends CartPaymentMethodUpdated>({
    cartId,
    paymentMethodType,
  }: CartPaymentMethodUpdate): Promise<T> {
    return this.apiClient
      .patch(`cart_payments/${cartId}`, {
        paymentMethodType,
      })
      .then((response) => response.data)
  }

  public updateCartPaymentSepaInfos<T extends CartPaymentSepaUpdated>({
    cartId,
    iban,
    bic,
    ownerName,
    phone,
    mandateId,
  }: CartPaymentSepaUpdate): Promise<T> {
    return this.apiClient
      .patch(`cart_payment_sepas/${cartId}`, {
        iban,
        bic,
        ownerName,
        phone,
        mandateId,
      })
      .then((response) => response.data)
  }

  public getPaymentMethods(cartId: string): Promise<AdyenPaymentMethodsResponse> {
    return this.apiClient
      .get<AdyenPaymentMethodsResponse>(`carts/${cartId}/payment-methods`)
      .then((response) => response.data)
  }

  public initiatePayment(payload: AdyenInitiatePaymentPayload): Promise<AdyenInitiatePaymentResponse> {
    return this.apiClient
      .post<AdyenInitiatePaymentResponse>('carts/payments/initiate', payload)
      .then((response) => response.data)
  }

  public submitPaymentDetails(payload: AdyenSubmitDetailsPayload): Promise<AdyenInitiatePaymentResponse> {
    return this.apiClient
      .post<AdyenInitiatePaymentResponse>('carts/payments/details', payload)
      .then((response) => response.data)
  }

  public updateCustomerInfoInLogisticOrders(cartId: string): Promise<void> {
    return this.apiClient
      .post<void>(`carts/${cartId}/logistic-orders/customer-info`, {})
      .then((response) => response.data)
  }

  public updateEcoTaxInLogisticOrders(cartId: string): Promise<void> {
    return this.apiClient
      .post<void>(`carts/${cartId}/logistic-orders/eco-tax`, {})
      .then((response) => response.data)
  }
}
