import { defineStore } from 'pinia'
import { BannerSearch } from '@/vuejs/types/BannerSearch'
import BannerSearchHttpClient from '@/vuejs/services/httpclient/BannerSearchHttpClient'

export interface BannerSearchStoreState {
  bannersSearch: BannerSearch[]
}

export const useBannerSearchStore = defineStore({
  id: 'bannerSearch',

  state: (): BannerSearchStoreState => ({
    bannersSearch: [],
  }),

  actions: {
    async init() {
      try {
        this.bannersSearch =
          await BannerSearchHttpClient.get().getBannersSearch()
      } catch (error) {}
    },
  },
})
