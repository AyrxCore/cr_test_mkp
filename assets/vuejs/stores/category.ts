import { defineStore } from 'pinia'
import CategoryHttpClient from '@/vuejs/services/httpclient/CategoryHttpClient'
import { ref } from 'vue'
import { Category } from '@/vuejs/types/Product/Category'

export interface CategoryStoreState {
  categories: []
  listMenu: []
}

export const useCategoryStore = defineStore({
  id: 'category',
  state: (): CategoryStoreState => ({
    categories: [],
    listMenu: [],
  }),

  actions: {
    async init() {
      try {
        if (this.categories.length === 0) {
          const result = await CategoryHttpClient.get().getCategories()
          this.listMenu = result.slice(0, 6)
          this.categories = result.sort((a: Category, b: Category) => {
            return a.name.localeCompare(b.name)
          })
        }
      } catch (error) {
        return []
      }
    },
  },
  getters: {
    getAllCategories() {
      return this.categories
    },
  },
})
