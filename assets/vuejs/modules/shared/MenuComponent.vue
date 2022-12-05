<template>
  <div class="mt-7 items-center justify-between text-white md:flex">
    <div class="content">
      <button
        id="menu-button-categorie"
        class="menu-button flex w-[2rem] items-center rounded px-3 py-1 hover:opacity-75"
        @click="onClick('open')"
      >
        <MenuIconComponent class="text-lg" />
        Toutes les catégories
      </button>
      <div id="hamburger-menu-categorie" class="hamburger-menu">
        <nav class="w-full">
          <a href="#" class="font-bold hover:bg-gray-200"
            >Voir toutes les catégories</a
          >
          <hr class="border-b-{primary}-700 mx-auto mt-1 mb-1 w-[95%] border" />
          <div
            v-for="(categorie, id) in listCategories"
            :key="id"
            class="w-[100%] items-center py-1"
          >
            <a href="#" class="items-center hover:bg-gray-200">
              <span class="-mt-2">{{ categorie }}</span>
              <ChevronRightIconComponent
                class="primary float-right -mt-2 text-lg"
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
    <div v-for="(menu, id) in listMenu" :key="id" class="flex px-2">
      <a
        href="#"
        class="text-cotext hover:border-b-2 hover:border-purple-600"
        >{{ menu }}</a
      >
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

const onClick = (action): void => {
  const overlay = document.querySelector('#overlay-categorie')
  const button = document.querySelector('#menu-button-categorie')
  const menu = document.querySelector('#hamburger-menu-categorie')
  animateMenu(action, overlay, button, menu)
}

const animateMenu = (menuToggle: string, overlay, button, menu) => {
  if (menuToggle === 'open') {
    overlay.classList.add('open')
    menu.classList.add('open')
    button.classList.add('on')
  }

  if (menuToggle === 'close') {
    button.classList.remove('on')
    overlay.classList.remove('open')
    menu.classList.remove('open')
  }
}
</script>
