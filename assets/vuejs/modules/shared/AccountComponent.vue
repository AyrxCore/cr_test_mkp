<template>
  <div class="items-center text-white">
    <div class="inline-flex">
      <UserCheckIconComponent class="mr-1" />
      Bonjour {{ user.lastName }} {{ user.firstName }}
    </div>
    <div class="ml-6">
      <div class="content">
        <button
          id="menu-button-account"
          class="ml-2 flex items-center rounded hover:opacity-75"
          @click="onClick('open')"
        >
          Mon compte
        </button>
        <div
          id="hamburger-menu-account"
          class="hamburger-menu w-[220px!important]"
        >
          <nav class="w-full">
            <a href="#" class="ml-2 inline-flex font-bold hover:bg-gray-200">
              <UserIconComponent class="mr-2" />
              Mon compte
            </a>
            <hr class="mx-auto mt-1 mb-1 w-[95%] border border-b-primary" />
            <div
              v-for="(value, id) in listAccount"
              :key="id"
              class="w-[100%] items-center py-1"
            >
              <a href="#" class="inline-flex items-center hover:bg-gray-200">
                <ChevronRightIconComponent
                  class="-mt-2 mr-2 text-lg text-primary"
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
    <div class="inline-flex w-[18rem]">
      <MapInIconComponent class="mr-2" />
      <a href="#" class="text-xs">
        Livré à
        {{ user.account.buyer.default_address.street }}
        {{ user.account.buyer.default_address.postcode }}
        {{ user.account.buyer.default_address.city }}
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { animateSubMenu } from '@/vuejs/services/utils'
import UserCheckIconComponent from '@/vuejs/modules/shared/icon/UserCheckIconComponent.vue'
import UserIconComponent from '@/vuejs/modules/shared/icon/UserIconComponent.vue'
import DisconnectIconComponent from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import MapInIconComponent from '@/vuejs/modules/shared/icon/MapInIconComponent.vue'
import {useUserStore} from "@/vuejs/stores/user";
import {storeToRefs} from "pinia";

const userStore = useUserStore()
const {user} = storeToRefs(userStore)
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
