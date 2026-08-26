import { defineStore } from 'pinia'
import { BannerSearch } from '@/vuejs/types/BannerSearch'
import BannerSearchHttpClient from '@/vuejs/services/httpclient/BannerSearchHttpClient'

export interface BannerSearchStoreState {
  bannersSearch: BannerSearch[]
}

export const useBannerSearchStore = defineStore('bannerSearch', {
  state: (): BannerSearchStoreState => ({
    bannersSearch: [],
  }),

  actions: {
    async init() {
      try {
        this.bannersSearch =
          await BannerSearchHttpClient.get().getBannersSearch()
      } catch (_error) {
        // Ignored: banner search data is optional
      }
    },
  },
})
