<template>
  <div class="mt-7 items-center md:flex justify-between text-white">
    <div class="content">
      <button
        id="menu-button"
        class="flex px-3 py-1 rounded hover:opacity-75 w-[2rem] items-center"
        @click="onClick('open')"
      >
        <i class="fa fa-bars text-purple-600 text-lg mr-2" />
        Toutes les catégories
      </button>
      <div id="hamburger-menu">
        <nav class="w-full">
          <a href="#" class="font-bold hover:bg-gray-200">Voir toutes les catégories</a>
          <hr class="border border-b-{primary}-700 w-[95%] mx-auto mt-1 mb-1"/>
          <div
             v-for="(categorie, id) in listCategories"
             :key="id"
              class="py-1 w-[100%] items-center"
          >
            <a  href="#" class="hover:bg-gray-200 items-center ">
              <span class="-mt-2">{{ categorie }}</span>
              <i class="fa fa-chevron-right -mt-2 primary text-lg float-right" />
            </a>

          </div>
        </nav>
      </div>
      <div
        id="overlay"
        @click="onClick('close')"
      ></div>
    </div>
    <div v-for="(menu, id) in listMenu" :key="id" class="flex h-[25px] px-2">
      <a href="#" class="hover:border-b-2 hover:border-purple-600">{{ menu }}</a>
    </div>
  </div>
</template>

<script lang="ts" setup>

import { ref } from 'vue'

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

const listCategories = ref<string[]>([
  'Avantages salariés',
  'Certifications',
  'Chimie des bâtiments et des process',
  'Conseils',
  'Eclairage',
  'Emballages et expédition',
  'Energie',
  'Equipements atelier',
  'Equipements de protection individuelle (EPI)',
  'Formation',
  'Fournitures de bureau',
  'Gestion des déchets',
  'Hygiène et produits d\'entretien',
  'Informatique et burautique',
])

const onClick = (action): void => {
  const overlay = document.querySelector('#overlay')
  const button = document.querySelector('#menu-button')
  const menu = document.querySelector('#hamburger-menu')
  animateMenu(action, overlay, button, menu)

}

const animateMenu = (menuToggle :string, overlay, button, menu) => {
  if(menuToggle === 'open') {
    overlay.classList.add('open')
    menu.classList.add('open')
    button.classList.add('on')
  }

  if(menuToggle === 'close') {
    button.classList.remove('on')
    overlay.classList.remove('open')
    menu.classList.remove('open')
  }
}
</script>
