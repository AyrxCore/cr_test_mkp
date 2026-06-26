import { defineStore } from 'pinia'
import CategoryHttpClient from '@/vuejs/services/httpclient/CategoryHttpClient'
import { Category } from '@/vuejs/types/Product/Category'

export interface CategoryStoreState {
  categories: Category[]
  isLoaded: boolean
}

export const useCategoryStore = defineStore('category', {
  state: (): CategoryStoreState => ({
    categories: [],
    isLoaded: false,
  }),

  actions: {
    async getAllCategories() {
      try {
        if (this.categories.length === 0) {
          this.categories = await CategoryHttpClient.get().getCategories()
        }
      } catch {
        return []
      } finally {
        this.isLoaded = true
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
