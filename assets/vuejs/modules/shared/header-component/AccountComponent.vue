<template>
  <div class="flex items-center justify-center md:min-w-[400px]">
    <div class="flex items-center justify-between">
      <div class="md:flex md:flex-col md:justify-around">
        <div class="flex cursor-pointer items-center" @click.stop="toggleMenu">
          <UserPlainIconComponent />
          <div class="hidden text-lg md:ml-3 md:block md:max-w-[250px]">
            Bienvenue {{ user.firstName }} {{ user.lastName }}
          </div>
          <ArrowDownIconComponent class="ml-2" />
        </div>
        <MenuAccountComponent
          v-if="isMenuOpen"
          v-model="isMenuOpen"
          class="modal"
        />
      </div>
      <RouterLink
        :to="{ name: CartPageList.CART_RECAP }"
        class="relative z-10 ml-4 self-center"
        @click="sendGaEvent('click_header_cart')"
      >
        <div
          v-if="cartStore.nbProducts > 0"
          class="badge badge-sticked badge-red"
        >
          {{ cartStore.nbProducts }}
        </div>
        <ShoppingCartIcon />
      </RouterLink>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'

import ShoppingCartIcon from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import MenuAccountComponent from '@/vuejs/modules/shared/header-component/MenuAccountComponent.vue'
import UserPlainIconComponent from '@/vuejs/modules/shared/icon/UserPlainIconComponent.vue'
import ArrowDownIconComponent from '@/vuejs/modules/shared/icon/ArrowDownIconComponent.vue'

import { CartPageList } from '@/vuejs/router/pages-list'
import { useUserStore } from '@/vuejs/stores/user'
import { useCartStore } from '@/vuejs/stores/cart'

import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const userStore = useUserStore()
const cartStore = useCartStore()

const { user } = storeToRefs(userStore)

const isMenuOpen = ref<boolean>(false)

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
  sendGaEvent('click_header_account')
}
</script>
