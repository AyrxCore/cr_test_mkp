import { defineStore } from 'pinia'
import { SemanticButton } from '@/vuejs/types/SemanticButton'
import SemanticButtonsHttpClient from '@/vuejs/services/httpclient/SemanticButtonsHttpClient'

export interface SemanticButtonsStoreState {
  semanticButtonsConfig: SemanticButton[]
}

export const useSemanticButtonsStore = defineStore('semanticButtons', {
  state: (): SemanticButtonsStoreState => ({
    semanticButtonsConfig: [],
  }),

  actions: {
    async init() {
      if (this.semanticButtonsConfig.length === 0) {
        try {
          this.semanticButtonsConfig =
            await SemanticButtonsHttpClient.get().getSemanticButtons()
        } catch (_error) {
          // Ignored: semantic buttons are optional
        }
      }
    },
  },
  getters: {
    semanticButtonsSectionTitle() {
      return this.semanticButtonsConfig.find(
        (sb) => typeof sb.sectionTitle !== 'undefined',
      )
    },
    semanticButtons() {
      return this.semanticButtonsConfig
        .filter(
          (sb) =>
            typeof sb.sectionTitle === 'undefined' &&
            typeof sb.label !== 'undefined' &&
            typeof sb.search !== 'undefined',
        )
        .sort((a, b) => a.position > b.position)
    },
  },
})
