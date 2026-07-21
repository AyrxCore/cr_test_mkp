<template>
  <div class="text-sm italic lg:text-base">
    <SkeletonLoading v-if="isLoading" />
    <template v-else-if="shippingMessages.length">
      <p
        v-for="(msg, index) in shippingMessages"
        :key="index"
        :class="msg.success ? 'text-green-600' : ''"
      >
        {{ msg.text }}
      </p>
    </template>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import { CartOrder } from '@/vuejs/types/Cart'
import { getShippingMessages, ShippingMessage } from '@/vuejs/modules/cart/shippingMessages.ts'
import { PRODUCT_FDP_PREFIX } from '@/vuejs/services/const.ts'

import SkeletonLoading from '@/vuejs/modules/shared/SkeletonLoading.vue'

const props = defineProps({
  cartOrder: {
    required: true,
    type: Object as PropType<CartOrder>,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
})

const isMissingProductFdp = computed<boolean>(() => {
  const shippingCost = props.cartOrder.shippingCostResult?.shippingCost ?? 0
  const hasProductFdp = props.cartOrder.products.some((p) =>
    p.externalId?.startsWith(PRODUCT_FDP_PREFIX),
  )
  return shippingCost > 0 && !hasProductFdp
})

const shippingMessages = computed<ShippingMessage[]>(() => {
  const result = props.cartOrder.shippingCostResult

  if (result?.type === 'PERCENTAGE') {
    return []
  }

  if (isMissingProductFdp.value) {
    return [{ text: 'Vous bénéficiez de la livraison gratuite', success: true }]
  }

  if (!result) return []
  return getShippingMessages(result)
})
</script>
