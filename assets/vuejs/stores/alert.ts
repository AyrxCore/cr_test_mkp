import { defineStore } from 'pinia'
import { AlertStoreState, AlertType } from '@/vuejs/types/Alert'

export const useAlertStore = defineStore({
  id: 'alert',

  state: (): AlertStoreState => ({
    show: false,
    message: '',
    type: undefined,
    timeout: null,
  }),

  actions: {
    setShow(message: string, type: AlertType): void {
      clearTimeout(this.timeout)
      this.message = message
      this.type = type
      this.show = true
      this.timeout = setTimeout(
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
