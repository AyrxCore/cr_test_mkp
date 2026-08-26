import { defineStore } from 'pinia'
import LegalContentHttpClient from '../services/httpclient/LegalContentHttpClient'
import type { LegalContent } from '../types/LegalContent'

interface LegalContentState {
  legalContent: LegalContent | null
  isLoading: boolean
}

export const useLegalContentStore = defineStore('legalContent', {
  state: (): LegalContentState => ({
    legalContent: null,
    isLoading: false,
  }),

  actions: {
    async fetch(): Promise<void> {
      this.isLoading = true
      try {
        const response = await LegalContentHttpClient.get().getLegalContent()
        this.legalContent = response.data
      } catch (_error) {
        this.legalContent = null
      } finally {
        this.isLoading = false
      }
    },
  },
})


