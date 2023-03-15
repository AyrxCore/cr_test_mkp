<template>
  <div class="flex items-center justify-center text-white md:min-w-[400px]">
    <div class="flex justify-between">
      <div class="md:flex md:flex-col md:justify-around">
        <UserCheckIcon class="cursor-pointer" @click.stop="toggleMenu" />
        <MapInIcon class="hidden md:block" />
        <MenuAccount v-model="isMenuOpen" class="sm:hidden" />
      </div>
      <div class="md:ml-3 md:max-w-[250px]">
        <div class="sr-only items-center md:not-sr-only">
          Bonjour {{ user.lastName }} {{ user.firstName }}
          <div class="relative">
            <button
              id="menu-button-account"
              class="flex items-center rounded font-bold hover:opacity-75"
              @click.stop="toggleMenu"
            >
              <span class="sr-only md:not-sr-only">Mon compte</span>
            </button>

            <MenuAccount v-model="isMenuOpen" class="hidden sm:block" />
          </div>
        </div>
        <div class="sr-only inline-flex items-center md:not-sr-only">
          <a v-if="companyStore.defaultBillingAddress" href="#" class="text-xs">
            Livré à
            {{ companyStore.defaultShippingAddress.company }},
            {{ companyStore.defaultShippingAddress.street }}
            {{ companyStore.defaultShippingAddress.postcode }}
            {{ companyStore.defaultShippingAddress.city }}
          </a>
        </div>
      </div>
      <!-- <a class="ml-4 self-center md:ml-0" href="#">
        <HeartIcon />
      </a> -->
      <RouterLink
        :to="{ name: CartPageList.RECAP }"
        class="relative ml-4 self-center"
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
import { CartPageList } from '@/vuejs/router/pages-list'
import UserCheckIcon from '@/vuejs/modules/shared/icon/UserCheckIconComponent.vue'
import MapInIcon from '@/vuejs/modules/shared/icon/MapInIconComponent.vue'
import ShoppingCartIcon from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import HeartIcon from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import MenuAccount from '@/vuejs/modules/shared/header-component/MenuAccountComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { useCartStore } from '@/vuejs/stores/cart'
import { storeToRefs } from 'pinia'
import { useBuyerCompanyStore } from '@/vuejs/stores/buyer_company'

const userStore = useUserStore()
const cartStore = useCartStore()
const companyStore = useBuyerCompanyStore()

const { user } = storeToRefs(userStore)

const isMenuOpen = ref<boolean>(false)

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
}
</script>
