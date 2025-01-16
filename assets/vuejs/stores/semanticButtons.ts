import { defineStore } from 'pinia'
import { SemanticButton } from '@/vuejs/types/SemanticButton'
import SemanticButtonsHttpClient from '@/vuejs/services/httpclient/SemanticButtonsHttpClient'

export interface SemanticButtonsStoreState {
  semanticButtons: SemanticButton[]
}

export const useSemanticButtonsStore = defineStore({
  id: 'semanticButtons',

  state: (): SemanticButtonsStoreState => ({
    semanticButtons: [],
  }),

  actions: {
    async init() {
      if (this.semanticButtons.length === 0) {
        try {
          this.semanticButtons =
            await SemanticButtonsHttpClient.get().getSemanticButtons()
        } catch (error) {}
      }
    },
  },
  getters: {
    semanticButtonsSectionTitle() {
      return this.semanticButtons.find(
        (sb) => typeof sb.sectionTitle !== 'undefined',
      )
    },
    getSemanticButtons() {
      return this.semanticButtons.filter(
        (sb) => typeof sb.sectionTitle === 'undefined',
      )
    },
  },
})
