<template>
  <div class="flex items-center text-white justify-end">
    <div class="w-auto md:w-[18rem]">
      <div class="inline-flex items-center sr-only md:not-sr-only">
        <UserCheckIconComponent class="mr-1" />
        Bonjour {{ username }}
      </div>
      <div class="ml-6 w-full pr-3">
        <div class="flex justify-end md:justify-start">
          <button
            id="menu-button-account"
            class="flex items-center rounded hover:opacity-75  md:ml-2"
            @click="onClick('open')"
          >
            <UserCheckIconComponent class="mr-1 md:mr-0 md:sr-only" />
            <span class="sr-only md:not-sr-only">Mon compte</span>
          </button>
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
              <a href="#" class="mt-3 inline-flex font-bold hover:bg-gray-200">
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
      <div class="inline-flex items-center sr-only md:not-sr-only">
        <MapInIconComponent class="mr-2" />
        <a href="#" class="text-xs">Livré à {{ address }}</a>
      </div>
    </div>

    <div class="ml-3 flex text-white items-center">
      <div class="mr-1 md:mr-4 flex">
        <a href="#">
          <HeartIconComponent  />
        </a>
      </div>
      <div class="flex">
        <a href="/app/cart">
          <div class="absolute w-5 h-5 flex items-center text-primary ml-4 -mt-2 bg-white pl-1 rounded-full">3</div>
          <ShoppingCartIconComponent />
        </a>
      </div>
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
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'

const listAccount = ref<string[]>([
  'Historique des commandes',
  'Mes factures',
  'Bons de livraison',
  'Validation de commande',
  'Mes coordonnées',
  'Changer de SIRET',
])

const username = ref<string>('Qantis')
const address = ref<string>('185, allée des Cyprès, 69760')

const onClick = (action): void => {
  const overlay = document.querySelector('#overlay-account')
  const button = document.querySelector('#menu-button-account')
  const menu = document.querySelector('#hamburger-menu-account')
  animateSubMenu(action, overlay, button, menu)
}
</script>
