<template>
  <div class="text-cotext items-center text-white">
    <div class="inline-flex">
      <UserCheckIconComponent class="mr-1" />
      Bonjour {{ username }}
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
          class="hamburger-menu flex w-[300px!important]"
        >
          <nav class="flex w-full flex-col">
            <a
              href="/app/account"
              class="flex-row items-center font-bold hover:bg-gray-200"
            >
              <UserIconComponent class="mr-2" />
              <span>Mon compte</span>
            </a>
            <hr
              class="border-b-{primary}-700 mx-auto mt-1 mb-1 w-[95%] border"
            />
            <div
              v-for="(value, id) in listAccount"
              :key="id"
              class="flex flex-row items-center py-1"
            >
              <a href="#" class="flex flex-row items-center hover:bg-gray-200">
                <ChevronRightIconComponent
                  :stroke-color="'#050056'"
                  class="-mt-2 mr-2 text-lg"
                />
                <span class="-mt-2">{{ value }}</span>
              </a>
            </div>
            <hr
              class="border-b-{primary}-700 mx-auto mt-1 mb-1 w-[95%] border"
            />
            <a href="#" class="mt-3 flex font-bold hover:bg-gray-200">
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
      <a href="#" class="text-xs">Livré à {{ address }}</a>
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
