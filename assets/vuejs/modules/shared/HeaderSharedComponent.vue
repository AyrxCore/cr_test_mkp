<template>
  <nav class="header sticky top-0 z-30 bg-white py-2 px-4 md:py-4">
    <div class="mx-auto mt-5 w-[100%] px-4 md:flex md:items-center">
      <div class="sm:mx-auto sm:mb-2 sm:w-[100%] md:w-[10%]">
        <a href="#" class="text-xl font-bold text-white">
          <img
            :src="qantisLogoImg"
            alt="header"
            class="logo-qantis-header sm:mx-auto sm:mb-2"
          />
        </a>
      </div>

      <div
        id="navbar-collapse"
        class="w-[90%] items-center justify-end text-white md:flex"
      >
        <div
          class="mx-auto mr-10 ml-5 h-[55px] min-w-max grow flex-row items-center md:flex"
        >
          <SearchComponent
            placeholder="Recherchez un produit, un accord-cadre ou un fournisseur"
          />
        </div>
        <AccountComponent />
        <div class="ml-3 flex text-white">
          <div class="mr-4 flex">
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
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import { getImage } from '@/vuejs/services/utils'
import qantisHeaderLogo from '@/vuejs/assets/img/qantis_marketplace_logo_blanc.png'
import SearchComponent from '@/vuejs/modules/shared/SearchComponent.vue'
import MenuComponent from '@/vuejs/modules/shared/MenuComponent.vue'
import AccountComponent from '@/vuejs/modules/shared/AccountComponent.vue'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const qantisLogoImg = getImage(qantisHeaderLogo)
</script>
