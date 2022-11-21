<template>
  <div class="items-center text-white">
    <div>
      <i class="fa fa-user-check" />
      Bonjour {{ username }}
    </div>
    <div class="ml-6">
      <div class="content">
        <button
          id="menu-button-account"
          class="flex items-center rounded hover:opacity-75"
          @click="onClick('open')"
        >
          Mon compte
        </button>
        <div
          id="hamburger-menu-account"
          class="hamburger-menu w-[200px!important]"
        >
          <nav class="w-full">
            <a href="#" class="font-bold hover:bg-gray-200">
              <i class="far fa-user mr-1" />
              Mon compte
            </a>
            <hr
              class="border-b-{primary}-700 mx-auto mt-1 mb-1 w-[95%] border"
            />
            <div
              v-for="(value, id) in listAccount"
              :key="id"
              class="w-[100%] items-center py-1"
            >
              <a href="#" class="items-center hover:bg-gray-200">
                <i class="fa fa-chevron-right primary -mt-2 mr-2 text-lg" />
                <span class="-mt-2">{{ value }}</span>
              </a>
            </div>
            <hr
              class="border-b-{primary}-700 mx-auto mt-1 mb-1 w-[95%] border"
            />
            <a href="#" class="mt-3 font-bold hover:bg-gray-200">
              <i class="fas fa-sign-out-alt mr-1" />
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
    <div class="w-[18rem]">
      <i class="fa fa-location-dot mr-3" />
      <a href="#" class="text-xs">Livré à {{ address }}</a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { animateSubMenu } from '@/vuejs/services/utils'

const listAccount = ref<string[]>([
  'Historique des commandes',
  'Mes factures',
  'Bons de livraison',
  'Validation de commande',
  'Mes coordonnées',
  'Changer de SIRET',
])

const username = ref<string>('Qantis')
const address = ref<string>('185, allée des Cyprès, 69760 LIMONEST')

const onClick = (action): void => {
  const overlay = document.querySelector('#overlay-account')
  const button = document.querySelector('#menu-button-account')
  const menu = document.querySelector('#hamburger-menu-account')
  animateSubMenu(action, overlay, button, menu)
}
</script>
