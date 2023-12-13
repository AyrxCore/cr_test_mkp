import { defineStore } from 'pinia'
import ExpertContentHttpClient from '@/vuejs/services/httpclient/ExpertContentHttpClient'
import {
  ExpertContent,
  ExpertContentCategory,
} from '@/vuejs/types/ExpertContent'

export interface ExpertContentStoreState {
  categories: ExpertContentCategory[]
  expertContents: ExpertContent[]
  slug: string
}

export const useExpertContentStore = defineStore({
  id: 'expertContent',

  state: (): ExpertContentStoreState => ({
    categories: [],
    expertContents: [],
    slug: null,
  }),

  actions: {
    async init(): Promise<ExpertContent[]> {
      if (!this.expertContents.length) {
        await this.findExpertsContents()
      }
      if (!this.categories.length) {
        await this.findExpertsContentsCategories()
      }
      this.expertContents.forEach((expertContent: ExpertContent) => {
        const category = this.getCategoryColorByName(expertContent.categoryName)
        expertContent.categoryColor = category.color
      })

      return this.expertContents
    },

    async initExpertContent(slug: string) {
      if (!this.categories.length) {
        await this.findExpertsContentsCategories()
      }
      let currentExpertContent = null
      this.expertContents.forEach((expertContent: ExpertContent) => {
        if (expertContent.slug === slug) {
          currentExpertContent = expertContent
        }
      })
      if (!currentExpertContent) {
        try {
          currentExpertContent =
            await ExpertContentHttpClient.get().getExpertContent(slug)
          const category = this.getCategoryColorByName(
            currentExpertContent.categoryName,
          )
          currentExpertContent.categoryColor = category.color
        } catch (error) {}
      }

      return currentExpertContent
    },

    async findExpertsContentsCategories(): Promise<[]> {
      try {
        this.categories =
          await ExpertContentHttpClient.get().findExpertsContentsCategories()
      } catch (error) {
        return []
      }
    },
    async findExpertsContents(): Promise<[]> {
      try {
        this.expertContents =
          await ExpertContentHttpClient.get().findExpertsContents()
      } catch (error) {
        return []
      }
    },
    getCategoryColorByName(categoryName: string) {
      return this.categories.find((category: ExpertContentCategory) => {
        if (category.name === categoryName) {
          return category.color
        }
        return null
      })
    },
  },

  getters: {
    getExpertsContentsCategories() {
      return this.categories
    },
  },
})
