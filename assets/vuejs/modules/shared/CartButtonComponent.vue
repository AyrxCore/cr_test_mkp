<template>
  <RouterLink
    :to="{ name: CartPageList.CART_RECAP }"
    class="relative z-10 self-center"
  >
    <div v-if="cartStore.nbProducts > 0" class="badge badge-sticked badge-red">
      {{ cartStore.nbProducts }}
    </div>
    <ShoppingCartIcon />
  </RouterLink>
</template>

<script lang="ts" setup>
import { CartPageList } from '@/vuejs/router/pages-list'
import { useCartStore } from '@/vuejs/stores/cart'

import ShoppingCartIcon from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'

const cartStore = useCartStore()
</script>
