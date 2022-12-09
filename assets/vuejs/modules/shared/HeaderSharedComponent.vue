<template>
  <!-- Bloc desktop menu -->
  <nav class="header sticky top-0 z-30 bg-primary md:py-0.5 px-4 hidden md:block">
    <div class="mx-auto px-4 md:flex md:items-center">
      <LogoComponent />
      <div
        class="mx-auto mr-10 ml-5 h-[55px] min-w-max grow flex-row md:flex md:w-[100%]"
      >
        <SearchComponent
          placeholder="Recherchez un produit, un accord-cadre ou un fournisseur"
        >
          <i class="fa fa-search" />
        </SearchComponent>
      </div>
      <div
        id="navbar-collapse"
        class="mt-3 items-center justify-end text-white md:flex"
      >
        <AccountComponent />
      </div>
    </div>
    <MenuComponent />
  </nav>
  <!-- Fin Bloc desktop menu -->

  <nav class="header sticky top-0 z-30 bg-primary px-4 w-full block md:hidden">
    <div class="mx-auto flex items-center flex-col">
      <div class="flex flex-row w-full">
        <MenuComponent class="w-4/12"/>
        <LogoComponent class="w-4/12 flex justify-center"/>
        <div
          id="navbar-collapse"
          class="w-4/12 mt-0 flex justify-end items-center text-white"
        >
          <AccountComponent />
        </div>
      </div>

      <div
        class="mx-auto mt-2 mr-0 ml-0 h-[55px] min-w-max grow flex-row w-full"
      >
        <SearchComponent
          placeholder="Recherchez un produit, un accord-cadre ou un fournisseur"
        >
          <SearchIconComponent />
        </SearchComponent>
      </div>
    </div>
  </nav>
</template>

<script lang="ts" setup>
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import SearchComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import MenuComponent from '@/vuejs/modules/shared/header-component/MenuComponent.vue'
import AccountComponent from '@/vuejs/modules/shared/header-component/AccountComponent.vue'
import LogoComponent from '@/vuejs/modules/shared/header-component/LogoComponent.vue'
import SearchIconComponent from '@/vuejs/modules/shared/icon/SearchIconComponent.vue'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';

.logo-qantis-header {
  @apply h-auto w-[145px] left-[60px] top-[24.5px];
}

.bg-gradient {
  @apply bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2;
}

.menu-button {
  @apply flex rounded z-50 md:w-[220px] md:h-[50px];
}

.overlay {
  display: none;
  height: 100vh;
  width: 100%;
  position: fixed;
  z-index: 99;
  opacity: 0;
  background-color: rgba(0, 0, 0, 0.4);
  bottom: 0;
  right: 0;
  left: 0;
  animation-duration: 300ms;
}

.overlay.open {
  display: block;
  opacity: 1;
  animation-duration: 300ms;
}

.hamburger-menu {
  height: 0;
  width: 0;
  background: #fff;
  position: fixed;
  top: inherit;
  z-index: 101;
  overflow: hidden;
  border-radius: 5px;
  font-family: CoText, sans-serif;

  nav {
    padding: 10px;
    z-index: 101;
    overflow-y: hidden;
    overflow-x: hidden;

    a {
      display: flex;
      padding: 10px;
      height: 2em;
      color: $primary;
      font-size: 1em;
      line-height: 1em;
      text-decoration: none;
      overflow: hidden;

      &:hover {
        cursor: pointer;
        border-radius: 10px;
      }
    }
  }
}

.hamburger-menu.open {
  width: 430px;
  height: auto;
  animation-duration: 300ms;
}
</style>
