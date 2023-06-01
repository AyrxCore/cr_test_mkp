
import { defineStore } from 'pinia'
import CategoryHttpClient from '@/vuejs/services/httpclient/CategoryHttpClient'
import {ref} from 'vue'

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
    getCategorieById(id: number, cats: Array) {
      const cat = ref<Object>()
      cat.value = cats.find((c) => c.id === id)
    },
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
