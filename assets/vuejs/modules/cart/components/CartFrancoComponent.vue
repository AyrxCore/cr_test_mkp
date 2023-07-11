<template>
  <div class="text-sm italic text-gray-500 lg:text-base">
    <p v-if="notDisplayedPromotion || !promotions.length">
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
import { PropType, computed, onMounted } from 'vue'

import { Order } from '@/vuejs/types/Cart'
import { Seller, SellerPromotion } from '@/vuejs/types/Seller'

import { formatPrice } from '@/vuejs/services/utils'

import { useSellerStore, SELLER_IDS } from '@/vuejs/stores/seller'

const sellerStore = useSellerStore()

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

onMounted(async (): Promise<void> => {
  const sellerId = props.order.seller.id
  await sellerStore.getSellerPromotions(sellerId)
})

const promotions = computed((): SellerPromotion[] => {
  return sellerStore.promotions[props.order.seller.id] || []
})

const seller = computed((): Seller => {
  return sellerStore.sellers.find((e) => e.id === props.order.seller.id)
})

const notDisplayedPromotion = computed((): boolean => {
  const SELLERS_NO_DISPLAY_PROMOTION = [SELLER_IDS.KRÖMM]
  return SELLERS_NO_DISPLAY_PROMOTION.includes(seller.value?.id)
})

const leftBeforePromotion = computed((): string => {
  if (!nextPromotion.value) return null
  return formatPrice(
    (nextPromotion.value.order_eligibility.amount -
      props.order.items_total_excluding_taxes) /
      100,
  )
})

const nextPromotion = computed((): SellerPromotion => {
  const total = props.order.items_total_excluding_taxes
  let currentPromotion: SellerPromotion = null
  if (!promotions.value.length) return
  promotions.value.forEach((p, id) => {
    if (!currentPromotion && total < p.order_eligibility.amount) {
      currentPromotion = p
    } else if (
      total < p.order_eligibility.amount &&
      currentPromotion.order_eligibility.amount > p.order_eligibility.amount
    ) {
      currentPromotion = p
    }
  })

  return currentPromotion
})

const hasReachedFranco = computed((): boolean => {
  return (
    promotions.value &&
    promotions.value.length > 0 &&
    nextPromotion.value === null
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
