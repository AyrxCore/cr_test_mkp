<template>
  <div
    class="top-3 flex items-center text-white lg:top-0 lg:mt-5 lg:w-auto lg:justify-around"
  >
    <div>
      <button
        id="menu-button-categorie"
        class="flex items-center rounded border-b-2 border-b-transparent py-1 hover:opacity-75 lg:px-3"
        @click="onClick('open')"
      >
        <MenuIconComponent class="mr-0.5 text-xl lg:w-auto" />
        <span class="ml-4 hidden lg:block">Toutes les catégories</span>
      </button>
      <div id="hamburger-menu-categorie" class="hamburger-menu">
        <nav class="w-full">
          <a href="#" class="font-bold hover:bg-gray-200">
            Voir toutes les catégories
          </a>
          <hr
            class="mx-auto mt-1 mb-1 w-[90%] border border-b-primary lg:w-[95%]"
          />
          <div
            v-for="(categorie, id) in listCategories"
            :key="id"
            class="w-[100%] items-center py-1"
          >
            <a href="#" class="items-center justify-between hover:bg-gray-200">
              <span class="-mt-2">{{ categorie }}</span>
              <ChevronRightIconComponent
                class="float-right -mt-2 text-lg text-primary"
                :stroke-color="'#050056'"
              />
            </a>
          </div>
        </nav>
      </div>
      <div
        id="overlay-categorie"
        class="overlay"
        @click="onClick('close')"
      ></div>
    </div>
    <div
      v-for="(menu, id) in listMenu"
      :key="id"
      class="sr-only flex px-2 lg:not-sr-only"
    >
      <a
        href="#"
        class="border-b-2 border-b-transparent hover:border-purple-600"
        >{{ menu }}
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { listCategories } from '@/vuejs/services/utils'
import MenuIconComponent from '@/vuejs/modules/shared/icon/MenuIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'

const listMenu = ref<string[]>([
  'Fournitures de bureau',
  'Téléphonie',
  'Energie',
  'Intérim',
  'Véhicules',
  'Outillage',
  'Quincaillerie',
  'EPI',
  'Location de matériel',
])
</script>
