<template>
  <div class="flex items-center justify-center text-white md:min-w-[400px]">
    <div class="w-auto">
      <div class="flex justify-between">
        <div class="md:flex md:flex-col md:justify-around">
          <UserCheckIconComponent
            class="cursor-pointer"
            @click="onClick('open')"
          />
          <MapInIconComponent class="hidden md:block" />
        </div>
        <div class="md:ml-3 md:max-w-[250px]">
          <div class="sr-only items-center md:not-sr-only">
            Bonjour {{ user.lastName }} {{ user.firstName }}
            <div>
              <button
                id="menu-button-account"
                class="flex items-center rounded font-bold hover:opacity-75"
                @click="onClick('open')"
              >
                <span class="sr-only md:not-sr-only">Mon compte</span>
              </button>
            </div>
          </div>
          <div class="sr-only inline-flex items-center md:not-sr-only">
            <a href="#" class="text-xs">Livré à
              {{ user.account.buyer.default_address.street }}
              {{ user.account.buyer.default_address.postcode }}
              {{ user.account.buyer.default_address.city }}
            </a>
          </div>
        </div>
        <a class="ml-4 self-center md:ml-0" href="#">
          <HeartIconComponent />
        </a>
        <RouterLink
          :to="{ name: CartPageList.CART }"
          class="relative ml-4 self-center"
        >
          <div class="badge badge-sticked badge-red">3</div>
          <ShoppingCartIconComponent />
        </RouterLink>
      </div>

      <div class="flex justify-end md:justify-start">
        <div
          id="hamburger-menu-account"
          class="hamburger-menu flex w-[300px!important]"
        >
          <nav class="flex w-full flex-col">
            <a
              href="/app/account"
              class="flex-row items-center font-bold hover:bg-gray-200"
            >
              <UserIconComponent class="mr-3" />
              <span>Mon compte</span>
            </a>
            <hr class="mx-auto mt-1 mb-1 w-[95%] border border-b-primary" />
            <div
              v-for="(value, id) in listAccount"
              :key="id"
              class="flex flex-row items-center py-1"
            >
              <a href="#" class="inline-flex items-center hover:bg-gray-200">
                <ChevronRightIconComponent
                  class="-mt-2 mr-2 stroke-primary text-lg text-primary"
                />
                <span class="-mt-2">{{ value }}</span>
              </a>
            </div>
            <hr class="mx-auto mt-1 mb-1 w-[95%] border border-b-primary" />
            <a
                href="#"
                class="mt-3 inline-flex font-bold hover:bg-gray-200"
                @click="onLogout($event)"
            >
              <DisconnectIconComponent class="mr-2" />
              Se déconnecter
            </a>
          </nav>
        </div>
        <div
          id="overlay-account"
          class="overlay"
          @click="onClick('close')"
        ></div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { animateSubMenu } from '@/vuejs/services/utils'
import { CartPageList } from '@/vuejs/modules/cart/routerCart'
import UserCheckIconComponent from '@/vuejs/modules/shared/icon/UserCheckIconComponent.vue'
import UserIconComponent from '@/vuejs/modules/shared/icon/UserIconComponent.vue'
import DisconnectIconComponent from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import MapInIconComponent from '@/vuejs/modules/shared/icon/MapInIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import {useUserStore} from "@/vuejs/stores/user";
import {storeToRefs} from "pinia";

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const listAccount = ref<string[]>([
  'Historique des commandes',
  'Mes factures',
  'Bons de livraison',
  'Validation de commande',
  'Mes coordonnées',
  'Changer de SIRET',
])


const onClick = (action): void => {
  const overlay = document.querySelector('#overlay-account')
  const button = document.querySelector('#menu-button-account')
  const menu = document.querySelector('#hamburger-menu-account')
  animateSubMenu(action, overlay, button, menu)
}

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  await userStore.logout() &&
  location.reload()
}
</script>
