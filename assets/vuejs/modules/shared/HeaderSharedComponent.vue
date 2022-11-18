<template>
  <nav class="bg-white py-2 md:py-4 header px-4 sticky top-0" >
    <div class=" px-4 mx-auto md:flex md:items-center mt-5  w-[100%]">
      <div class="md:w-[10%] sm:w-[100%] sm:mx-auto sm:mb-2">
          <a href="#" class="font-bold text-xl text-white">
            <img :src="qantisLogoImg" alt="header" class="logo-qantis-header sm:mx-auto sm:mb-2"/>
          </a>
      </div>

      <div id="navbar-collapse" class="md:flex text-white items-center w-[90%] justify-end">
        <div class="md:flex flex-row grow items-center h-[55px] mr-10 mx-auto min-w-max ml-5">
          <SearchComponent placeholder="Recherchez un produit, un accord-cadre ou un fournisseur" />
        </div>
        <div class="text-white items-center">
          <div>
            <i class="fa fa-user-check" />
            Bonjour {{ username }}
          </div>
          <div class="ml-6"><a href="#">Mon compte</a></div>
          <div class="w-[18rem]">
            <i class="fa fa-location-dot mr-3" />
            <a href="#" class="text-xs ">Livré à  {{ address }}</a></div>
        </div>
        <div class="text-white flex ml-3">
          <div class="flex mr-4">
            <a href="#">
              <i class="fa fa-heart" />
            </a>
          </div>
          <div class="flex">
            <a href="#">
              <i class="fa fa-shopping-cart" />
            </a>
          </div>
        </div>
      </div>

    </div>
    <MenuComponent />
  </nav>

</template>

<script lang="ts" setup>

import {useUserStore} from '@/vuejs/stores/user'
import {storeToRefs} from 'pinia'
import { getImage } from '@/vuejs/services/utils'
import { ref } from 'vue'
import qantisHeaderLogo from '@/vuejs/assets/img/qantis_marketplace_logo_blanc.png'
import SearchComponent from '@/vuejs/modules/shared/SearchComponent.vue'
import MenuComponent from '@/vuejs/modules/shared/MenuComponent.vue'

const userStore = useUserStore()
const {user} = storeToRefs(userStore)

const qantisLogoImg = getImage(qantisHeaderLogo)
const username = ref<string>('Qantis')
const address = ref<string>('185, allée des Cyprès, 69760 LIMONEST')

</script>
