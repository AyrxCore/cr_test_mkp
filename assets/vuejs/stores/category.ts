
import { defineStore } from 'pinia'
import CategoryHttpClient from '@/vuejs/services/httpclient/CategoryHttpClient'

export interface CategoryStoreState {
  categories: [],
  listMenu: [],
}

export const useCategoryStore = defineStore({
  id: 'category',
  state: (): CategoryStoreState => ({
    categories: [],
    listMenu: [],
  }),

  actions: {
    async initAllCategories() {
      try {
        if (this.categories.length === 0) {
          const result =  await CategoryHttpClient.get().getCategories()
          this.categories = result.categories
          this.listMenu = result.menu
        }
      } catch (error) {
        return []
      }
    },
  },
  getters: {
    getAllCategories() {
      return this.categories
    }
  }
})
