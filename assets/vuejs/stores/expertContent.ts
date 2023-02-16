import {defineStore} from 'pinia'
import {useAlertStore} from '@/vuejs/stores/alert'
import {AlertType} from '@/vuejs/types/Alert'
import {HttpStatusCodes} from '@/vuejs/types/HttpClient'
import {getErrorMessage} from '@/vuejs/services/login'
import ExpertContentHttpClient from '@/vuejs/services/httpclient/ExpertContentHttpClient'
import {ExpertContent, ExpertContentCategory} from '@/vuejs/types/ExpertContent'

export interface ExpertContentStoreState {
  expertsContentsCategories: ExpertContentCategory[]
  expertsContents: ExpertContent[],
  currentExpertContentSlug: string
  currentExpertContent: ExpertContent
}

export const useExpertContentStore = defineStore({
  id: 'expertContent',

  state: (): ExpertContentStoreState => ({
    expertsContentsCategories: [],
    expertsContents: [],
    currentExpertContentSlug: null,
    currentExpertContent: null,
  }),

  actions: {
    async initActualitePage(slug) {

      if (!this.expertsContentsCategories.length) {
        await this.findExpertsContentsCategories()
      }
      this.expertsContents.forEach((expertContent) => {
        if (expertContent.slug === slug) {
          this.currentExpertContent = expertContent
        }
      })
      if (!this.currentExpertContent) {
        this.currentExpertContent = await this.initExpertContent(slug)
      }
      const category = this.getCategoryColorByName(this.currentExpertContent.categoryName)
      this.currentExpertContent.categoryColor = category.color
      return this.currentExpertContent
    },
    async init() {
      if (!this.expertsContentsCategories.length) {
        await this.findExpertsContentsCategories()
      }
      if (!this.expertsContents.length) {
        await this.findExpertsContents()
      }
      this.expertsContents.forEach((expertContent) => {
        const category = this.getCategoryColorByName(expertContent.categoryName)
        expertContent.categoryColor = category.color
      })
      return this.expertsContents
    },
    async findExpertsContentsCategories(): Promise<[]> {
      try {
        this.expertsContentsCategories = await ExpertContentHttpClient.get().findExpertsContentsCategories()
        // return this.expertsContentsCategories
      } catch (error) {
        return []
      }
    },
    async findExpertsContents(): Promise<[]> {
      try {
        this.expertsContents = await ExpertContentHttpClient.get().findExpertsContents()
      } catch (error) {
        return []
      }
    },
    async initExpertContent(slug) {
      const alertStore = useAlertStore()
      try {
        return await ExpertContentHttpClient.get().getExpertContent(slug)
      } catch (error) {
        error.response.status === HttpStatusCodes.unauthorized &&
        alertStore.setShow(
          getErrorMessage(error.response.data.message),
          AlertType.danger,
        )
      }
      return null

    },
    getCategoryColorByName(categoryName) {
      return this.expertsContentsCategories.find(category => {
        if (category.name === categoryName) {

          return category.color
        }
        return null
      })
    },
  },

  getters: {
    getExpertsContents(): Array<ExpertContent> {
      console.log('getExpertsContents')
      console.log(this.expertsContents)
      return this.expertsContents
    },
    getExpertsContentsCategories() {
      return this.expertsContentsCategories
    },
  },
})
