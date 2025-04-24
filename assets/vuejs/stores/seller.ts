import { defineStore } from 'pinia'
import { Seller, SellerPromotion, SellerStoreState } from '@/vuejs/types/Seller'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'
import { notifyError } from '@/vuejs/services/utils'
import { useChannelStore } from '@/vuejs/stores/channel'

export const SELLER_IDS = {
  KROMM: 26,
}

export const useSellerStore = defineStore({
  id: 'seller',
  state: (): SellerStoreState => ({
    allSellers: [],
    sellersByParams: [],
    promotions: {},
  }),

  actions: {
    async getAllSellers(): Promise<Seller[]> {
      try {
        if (this.allSellers.length === 0) {
          let sellers = await SellerHttpClient.get().fetchSellersByParams({})
          const channelStore = useChannelStore()
          const suppliersList =
            channelStore.channel?.options?.SUPPLIER_PARTNERS_HOMEPAGE_LIST
          if (sellers.length > 0 && suppliersList) {
            sellers = suppliersList.split(',').reduce((acc, e) => {
              const seller = sellers.find((s) => s.id === parseInt(e))
              if (seller) acc.push(seller)
              return acc
            }, [])
          }
          this.allSellers = sellers
        }
        return this.allSellers
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du chargement des vendeurs, merci de contacter un administrateur.`,
        )
      }
    },
    async getSellersByParams(params = {}): Promise<Seller[]> {
      try {
        const paramKey = JSON.stringify(params)
        if (
          typeof this.sellersByParams[paramKey] === 'undefined' ||
          (typeof this.sellersByParams[paramKey] !== 'undefined' &&
            this.sellersByParams[paramKey].length === 0)
        ) {
          this.sellersByParams[paramKey] =
            await SellerHttpClient.get().fetchSellersByParams(params)
        }
        return this.sellersByParams[paramKey]
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du chargement des vendeurs, merci de contacter un administrateur.`,
        )
      }
    },
    async getSeller(id: number): Promise<void> {
      if (this.allSellers.find((e: Seller) => e.id === id)) return
      this.allSellers.push(await SellerHttpClient.get().getSeller(id))
    },
    async getSellerPromotions(sellerId: number): Promise<void> {
      if (this.promotions[sellerId]) return
      this.promotions[sellerId] =
        await SellerHttpClient.get().getSellerPromotions(sellerId)
    },
    async getSellersListing(params = {}): Promise<Seller[]> {
      try {
        return await SellerHttpClient.get().fetchSellersByParams(params)
      } catch (error) {
        notifyError(
          `Une erreur est survenue lors du chargement des vendeurs, merci de contacter un administrateur.`,
        )
      }
    },
  },
  getters: {
    getPromotions: (state) => {
      return (order) => state.promotions[order.seller.id] || []
    },
    getNextPromotion: (state) => {
      return (order) => {
        const total = order.items_total_excluding_taxes
        let currentPromotion: SellerPromotion = null
        if (!state.getPromotions(order).length) return
        state.getPromotions(order).forEach((p, id) => {
          if (!currentPromotion && total < p.order_eligibility.amount) {
            currentPromotion = p
          } else if (
            total < p.order_eligibility.amount &&
            currentPromotion.order_eligibility.amount >
              p.order_eligibility.amount
          ) {
            currentPromotion = p
          }
        })

        return currentPromotion
      }
    },
    getHasReachedFranco: (state) => {
      return (order) =>
        state.getPromotions(order) &&
        state.getPromotions(order).length > 0 &&
        state.getNextPromotion(order) === null
    },
  },
})
