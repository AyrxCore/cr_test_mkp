import BaseClientService from '@/vuejs/services/BaseClientService'
import { Invoice } from '@/vuejs/types/Order'

export default class OrderHttpClient extends BaseClientService {
  public getOrders<T extends []>(): Promise<T> {
    return this.apiClient
      .get<T>('orders')
      .then((response) => response.data)
  }

  public getOrderById<T extends []>(orderId: number): Promise<T> {
    return this.apiClient
      .get<T>(`orders/${orderId}`)
      .then((response) => response.data)
  }

  public getOrderInvoiceById<T extends []>(
    orderInvoiceId: number,
  ): Promise<T | Invoice> {
    return this.apiClient
      .get<T>(`invoices/${orderInvoiceId}/download`)
      .then((response) => response.data)
  }
}
