import { defineStore } from 'pinia'
import { Seller, SellerStoreState } from '@/vuejs/types/Seller'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'
import { notifyError } from '@/vuejs/services/utils'
import { useChannelStore } from '@/vuejs/stores/channel'

const pendingRequests = new Map<string, Promise<Seller[]>>()

export const useSellerStore = defineStore('seller', {
  state: (): SellerStoreState => ({
    allSellers: [],
    sellersByParams: [],
    promotions: {},
  }),

  actions: {
    async getAllSellers(): Promise<void> {
      const sellers = await this.getSellersByParams({})
      this.allSellers = sellers
    },

    async getSellersByParams(params = {}): Promise<Seller[]> {
      const paramKey = JSON.stringify(params)
      
      if (this.sellersByParams[paramKey]?.length > 0) {
        return this.sellersByParams[paramKey]
      }

      if (pendingRequests.has(paramKey)) {
        return pendingRequests.get(paramKey)!
      }

      const promise = SellerHttpClient.get()
        .fetchSellersByParams(params)
        .then((sellers) => {
          this.sellersByParams[paramKey] = sellers
          return sellers
        })
        .catch((error) => {
          notifyError(
            'Une erreur est survenue lors du chargement des vendeurs, merci de contacter un administrateur.',
          )
          throw error
        })
        .finally(() => {
          pendingRequests.delete(paramKey)
        })

      pendingRequests.set(paramKey, promise)
      return promise
    },
    async getSeller(id: string): Promise<void> {
      if (
        this.allSellers.find(
          (e: Seller) => e.externalId === id,
        )
      ) {
        return
      }
      this.allSellers.push(await SellerHttpClient.get().getSeller(id))
    },
    async getSellerPromotions(sellerId: string): Promise<void> {
      if (this.promotions[sellerId]) return
      this.promotions[sellerId] =
        await SellerHttpClient.get().getSellerPromotions(sellerId)
    },
  },
  getters: {
    carouselSellers(): Seller[] {
      const channelStore = useChannelStore()
      const suppliersList =
        channelStore.channel?.options?.SUPPLIER_PARTNERS_HOMEPAGE_LIST
      let sellers = this.allSellers
      if (this.allSellers.length > 0 && suppliersList) {
        const keys = suppliersList
          .split(',')
          .map((e) => e.trim())
          .filter(Boolean)
        sellers = keys.reduce((acc, key) => {
          const seller = this.allSellers.find(
            (s) => s.externalId && s.externalId.trim() === key,
          )
          if (seller) acc.push(seller)
          return acc
        }, [] as Seller[])
      }
      return sellers
    },
  },
})
