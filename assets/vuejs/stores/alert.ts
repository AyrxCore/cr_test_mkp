import { defineStore } from 'pinia'
import { AlertStoreState, AlertType } from '@/vuejs/types/Alert'

export const useAlertStore = defineStore({
  id: 'alert',

  state: (): AlertStoreState => ({
    show: false,
    message: '',
    type: undefined,
  }),

  actions: {
    setShow(message: string, type: AlertType): void {
      this.message = message
      this.type = type
      this.show = true
      setTimeout(
        () => {
          this.setClose()
        },
        type === AlertType.success ? 4000 : 10000,
      )
    },
    setClose(): void {
      this.show = false
      this.message = ''
      this.type = undefined
    },
  },

  getters: {},
})
