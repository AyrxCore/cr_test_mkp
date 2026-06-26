import { defineStore } from 'pinia'

import NewsHttpClient from '@/vuejs/services/httpclient/NewsHttpClient'
import type { News } from '@/vuejs/types/News'

export interface NewsStoreState {
  allNews: News[]
  isLoading: boolean
  isInitialized: boolean
  error: string | null
}

export const useNewsStore = defineStore('news', {
  state: (): NewsStoreState => ({
    allNews: [],
    isLoading: false,
    isInitialized: false,
    error: null,
  }),

  getters: {
    bannerNews(): News[] {
      return this.allNews.filter(
        (news) =>
          news.bannerImgDesktop?.filename && news.displayBanner === true,
      )
    },
  },

  actions: {
    async initialize(): Promise<News[]> {
      if (this.isInitialized) {
        return this.allNews
      }

      this.isLoading = true
      this.error = null

      try {
        const newsHttpClient = NewsHttpClient.get()
        const response = await newsHttpClient.getNews()
        this.allNews = response?.data || []
        this.isInitialized = true
        return this.allNews
      } catch (err) {
        this.error = err instanceof Error ? err.message : 'Failed to load news'
        return []
      } finally {
        this.isLoading = false
      }
    },

    getNewsBySlug(slug: string): News | undefined {
      return this.allNews.find(
        (news) => news.slug === slug || news.fullSlug === `news/${slug}`,
      )
    },

    async refresh(): Promise<News[]> {
      this.isInitialized = false
      return this.initialize()
    },

    reset(): void {
      this.allNews = []
      this.isInitialized = false
      this.isLoading = false
      this.error = null
    },
  },
})
