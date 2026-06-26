import BaseClientService from '@/vuejs/services/BaseClientService'
import { Order } from '@/vuejs/types/Order'

export default class OrderHttpClient extends BaseClientService {
  public getOrders<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('orders')
      .then((response) => response.data)
  }

  public getOrderById(orderId: number): Promise<Order> {
    return this.apiClient
      .get<Order>(`orders/${orderId}`)
      .then((response) => response.data)
  }
}
