import { defineStore } from 'pinia'
import router, { PageList } from '@/vuejs/router'

export const useErrorStore = defineStore('error', {
  actions: {
    handleProductNotFoundError(error) {
      if (error.response && error.response.status === 404) {
        router.push({ name: PageList.PAGE_NOT_FOUND })
      }
    },
  },
})
