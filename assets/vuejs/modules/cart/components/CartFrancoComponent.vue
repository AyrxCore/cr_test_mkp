<template>
  <div class="text-sm italic lg:text-base">
    <p v-if="notDisplayedPromotion || !getPromotions.length">
      {{ seller?.description }}
    </p>
    <p v-else-if="leftBeforePromotion">
      Il vous reste {{ leftBeforePromotion }}€ HT de commande pour bénéficier de
      {{ promotionType }}
    </p>
    <p v-else-if="hasReachedFranco" class="text-green-600">
      Franco de port atteint - vous bénéficiez de la livraison gratuite
    </p>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, PropType } from 'vue'
import { storeToRefs } from 'pinia'

import { Order } from '@/vuejs/types/Cart'
import { Seller } from '@/vuejs/types/Seller'

import { formatPrice } from '@/vuejs/services/utils'

import { SELLER_IDS, useSellerStore } from '@/vuejs/stores/seller'

const sellerStore = useSellerStore()

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const { getPromotions, getNextPromotion, getHasReachedFranco } =
  storeToRefs(sellerStore)

onMounted(async (): Promise<void> => {
  const sellerId = props.order.seller.id
  await sellerStore.getSellerPromotions(sellerId)
})

const seller = computed((): Seller => {
  return sellerStore.sellers.find((e) => e.id === props.order.seller.id)
})

const hasReachedFranco = computed((): boolean => {
  return getHasReachedFranco.value(props.order)
})

const notDisplayedPromotion = computed((): boolean => {
  const SELLERS_NO_DISPLAY_PROMOTION = [SELLER_IDS.KROMM]
  return SELLERS_NO_DISPLAY_PROMOTION.includes(seller.value?.id)
})

const nextPromotion = computed(() => {
  return getNextPromotion.value(props.order)
})

const leftBeforePromotion = computed((): string => {
  if (!nextPromotion.value) return null
  return formatPrice(
    (nextPromotion.value?.order_eligibility.amount -
      props.order.items_total_excluding_taxes) /
      100,
  )
})

const promotionType = computed((): string => {
  const condition = nextPromotion.value?.conditions[0]
  if (!condition) return
  if (condition.apply_type === 'percent') {
    if (condition.apply_value === 1.0) {
      return 'la livraison gratuite'
    } else {
      return `${condition.apply_value * 100}% de réduction sur la livraison `
    }
  } else if (condition.apply_type === 'amount') {
    return `${condition.apply_value / 100}€ de réduction sur la livraison `
  }
})
</script>
