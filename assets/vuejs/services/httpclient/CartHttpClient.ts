import BaseClientService from '@/vuejs/services/BaseClientService'

import {
  AddOrderItem,
  Cart,
  CartAddressesUpdate,
  OrderItemQuantityUpdate,
  OrderShippingUpdate,
  CartPaymentMethodUpdate,
  CartPaymentMethodUpdated,
} from '@/vuejs/types/Cart'

export default class CartHttpClient extends BaseClientService {
  public getCartAsBuyer<T extends Cart>(): Promise<T> {
    return this.apiClient.get<T>('buyer/cart').then((response) => response.data)
  }

  public addProductToCartAsBuyer<T extends []>(data: AddOrderItem): Promise<T> {
    return this.apiClient
      .post('order_items', data)
      .then((response) => response.data)
  }

  public addProductsToCartAsBuyer<T extends []>(data): Promise<T> {
    return this.apiClient
      .postForm('order_items/multiple', data)
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
        cartId: cartId,
        shippingId: shippingId,
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
        shippingAddressId: shippingAddressId,
        billingAddressId: billingAddressId,
      })
      .then((response) => response.data)
  }

  public updateCartPaymentMethod<T extends CartPaymentMethodUpdated>({
    cartId,
    paymentMethodId,
  }: CartPaymentMethodUpdate): Promise<T> {
    return this.apiClient
      .patch(`cart_payments/${cartId}`, {
        paymentMethodId: paymentMethodId,
      })
      .then((response) => response.data)
  }

  public findCartById<T extends []>(id: number): Promise<Cart> {
    return this.apiClient
      .get(`buyer/cart/${id}`)
      .then((response) => response.data)
  }
}
