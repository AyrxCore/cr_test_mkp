import { defineStore } from 'pinia'
import { Order, OrderStoreState } from '@/vuejs/types/Order'
import OrderHttpClient from '@/vuejs/services/httpclient/OrderHttpClient'
import { AlertType } from '@/vuejs/types/Alert'
import { useAlertStore } from '@/vuejs/stores/alert'

export const useOrderStore = defineStore('order', {
  state: (): OrderStoreState => ({
    orders: [],
  }),

  actions: {
    async getOrders() {
      try {
        this.orders = await OrderHttpClient.get().getOrders()
      } catch (error) {
        const alertStore = useAlertStore()
        alertStore.setShow(
          error.response.data['hydra:description'],
          AlertType.danger,
        )
      }
    },
    async getOrderById(orderId: number): Promise<Order | undefined> {
      try {
        return await OrderHttpClient.get().getOrderById(orderId)
      } catch (error) {
        const alertStore = useAlertStore()
        alertStore.setShow(
          error.response.data['hydra:description'],
          AlertType.danger,
        )
        return undefined
      }
    },
  },
})
