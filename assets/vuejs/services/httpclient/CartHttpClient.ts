import BaseClientService from '@/vuejs/services/BaseClientService'

import {
  Cart,
  CartAddressesUpdate,
  CartPaymentMethodUpdate,
  CartPaymentMethodUpdated,
  CartPaymentSepaUpdate,
  CartPaymentSepaUpdated,
  OrderItemQuantityUpdate,
  OrderShippingUpdate,
  ShippingMethod,
} from '@/vuejs/types/Cart'

export default class CartHttpClient extends BaseClientService {
  public getCartAsBuyer<T extends Cart>(): Promise<T> {
    return this.apiClient.get<T>('buyer/cart').then((response) => response.data)
  }

  public addProductsToCartAsBuyer<T extends []>(data): Promise<T> {
    return this.apiClient
      .postForm('order_items', data)
      .then((response) => response.data)
  }

  public updateCartAsBuyer<T extends []>(
    data: OrderItemQuantityUpdate,
  ): Promise<T> {
    return this.apiClient
      .patch(`order_items/${data.id}`, data)
      .then((response) => response.data)
  }

  public deleteProductFromCartAsBuyer<T extends []>(id: number): Promise<T> {
    return this.apiClient
      .delete(`order_items/${id}`)
      .then((response) => response.data)
  }

  public updateOrderShipping<T extends []>({
    cartId,
    shippingId,
    orderId,
  }: OrderShippingUpdate): Promise<T> {
    return this.apiClient
      .patch(`order_shippings/${orderId}`, {
        cartId,
        shippingId,
      })
      .then((response) => response.data)
  }

  public updateCartAdresses<T extends []>({
    cartId,
    shippingAddressId,
    billingAddressId,
  }: CartAddressesUpdate): Promise<T> {
    return this.apiClient
      .patch(`cart_addresses/${cartId}`, {
        shippingAddressId,
        billingAddressId,
      })
      .then((response) => response.data)
  }

  public updateCartPaymentMethod<T extends CartPaymentMethodUpdated>({
    cartId,
    paymentMethodId,
  }: CartPaymentMethodUpdate): Promise<T> {
    return this.apiClient
      .patch(`cart_payments/${cartId}`, {
        paymentMethodId,
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
        iban: iban,
        bic: bic,
        ownerName: ownerName,
        phone: phone,
        mandateId: mandateId,
      })
      .then((response) => response.data)
  }

  public findCartById<T extends Cart>(id: number): Promise<T> {
    return this.apiClient
      .get(`buyer/cart/${id}`)
      .then((response) => response.data)
  }

  public getCartShippingMethods<T extends ShippingMethod[]>(
    cartId: number,
  ): Promise<T> {
    return this.apiClient
      .get(`buyer/cart/${cartId}/shipments`)
      .then((response) => response.data)
  }
}
