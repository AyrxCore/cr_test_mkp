import { defineStore } from 'pinia'
import CategoryHttpClient from '@/vuejs/services/httpclient/CategoryHttpClient'
import { Category } from '@/vuejs/types/Product/Category'

export interface CategoryStoreState {
  categories: []
}

export const useCategoryStore = defineStore({
  id: 'category',
  state: (): CategoryStoreState => ({
    categories: [],
  }),

  actions: {
    async getAllCategories() {
      try {
        if (this.categories.length === 0) {
          this.categories = await CategoryHttpClient.get().getCategories()
        }
      } catch (error) {
        return []
      }
    },
  },
  getters: {
    categoriesSortedAlphabetically() {
      return this.categories.slice(0).sort((a: Category, b: Category) => {
        return a.name.localeCompare(b.name)
      })
    },
  },
})
