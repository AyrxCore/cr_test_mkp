<template>
  <RouterLink
    :to="{ name: CartPageList.CART_RECAP }"
    class="relative z-10 self-center"
    @click="sendGaEvent('click_header_cart')"
  >
    <div v-if="cartStore.nbProducts > 0" class="badge badge-sticked badge-red">
      {{ cartStore.nbProducts }}
    </div>
    <ShoppingCartIcon />
  </RouterLink>
</template>

<script lang="ts" setup>
import ShoppingCartIcon from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { useCartStore } from '@/vuejs/stores/cart'
import { CartPageList } from '@/vuejs/router/pages-list'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const cartStore = useCartStore()
</script>
