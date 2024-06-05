import { defineStore } from 'pinia'
import { Seller, SellerStoreState } from '@/vuejs/types/Seller'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'
import { notifyError } from '@/vuejs/services/utils'

export const SELLER_IDS = {
  KROMM: 26,
}

export const useSellerStore = defineStore({
  id: 'seller',
  state: (): SellerStoreState => ({
    sellers: [],
    promotions: {},
  }),

  actions: {
    async getSellers(params = {}) {
      try {
        this.sellers = await SellerHttpClient.get().fetchSellersByParams(params)
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du chargement du vendeur, merci de contacter un administrateur.`,
        )
      }
    },
    async getSeller(id: number): Promise<void> {
      if (this.sellers.find((e: Seller) => e.id === id)) return
      this.sellers.push(await SellerHttpClient.get().getSeller(id))
    },
    async getSellerPromotions(sellerId: number): Promise<void> {
      if (this.promotions[sellerId]) return
      this.promotions[sellerId] =
        await SellerHttpClient.get().getSellerPromotions(sellerId)
    },
  },
  getters: {},
})
