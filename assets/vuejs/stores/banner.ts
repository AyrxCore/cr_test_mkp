import { defineStore } from 'pinia'
import { Banner } from '@/vuejs/types/Banner'
import BannerHttpClient from '@/vuejs/services/httpclient/BannerHttpClient'

export interface BannerStoreState {
  banner: Banner
}

export const useBannerStore = defineStore({
  id: 'banner',

  state: (): BannerStoreState => ({
    banner: null,
  }),

  actions: {
    async init() {
      if (!this.banner) {
        try {
          this.banner = await BannerHttpClient.get().getBanner()
        } catch (error) {}
      }
    },
  },
})
