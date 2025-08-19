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
    async init(): Promise<void> {
      if (!this.expertContents.length) {
        await this.findExpertsContents()
      }
      if (!this.categories.length) {
        await this.findExpertsContentsCategories()
      }

      this.expertContents.forEach((expertContent: ExpertContent) => {
        const category = this.getCategoryColorByName(expertContent.categoryName)
        if (category) {
          expertContent.categoryColor = category.color
        }
      })
    },

    async initExpertContent(slug: string): Promise<ExpertContent | null> {
      if (!this.expertContents.length) {
        await this.init()
      }

      let currentExpertContent = this.expertContents.find(
        (expertContent: ExpertContent) => expertContent.slug === slug
      ) || null

      if (!currentExpertContent) {
        try {
          currentExpertContent = await ExpertContentHttpClient.get().getExpertContent(slug)

          if (currentExpertContent) {
            this.expertContents.push(currentExpertContent)

            const category = this.getCategoryColorByName(currentExpertContent.categoryName)
            if (category) {
              currentExpertContent.categoryColor = category.color
            }
          }
        } catch (error) {
          console.error('❌ Error loading expert content:', error)
          return null
        }
      }

      return currentExpertContent
    },

    async findExpertsContentsCategories(): Promise<void> {
      try {
        this.categories = await ExpertContentHttpClient.get().findExpertsContentsCategories()
      } catch (error) {
        console.error('❌ Error loading categories:', error)
        this.categories = []
      }
    },

    async findExpertsContents(): Promise<void> {
      try {
        this.expertContents = await ExpertContentHttpClient.get().findExpertsContents()
      } catch (error) {
        console.error('❌ Error loading expert contents:', error)
        this.expertContents = []
      }
    },

    getCategoryColorByName(categoryName: string): ExpertContentCategory | undefined {
      return this.categories.find((category: ExpertContentCategory) =>
        category.name === categoryName
      )
    },
  },

  getters: {
    getExpertsContentsCategories(): ExpertContentCategory[] {
      return this.categories
    },
  },
})
