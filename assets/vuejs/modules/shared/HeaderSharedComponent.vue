<template>
  <nav class="sticky top-0 z-30 bg-primary px-4 pt-5 pb-3 md:block">
    <div class="mx-auto items-center px-4 md:flex">
      <div class="flex justify-between">
        <MenuComponent class="md:hidden" />
        <LogoComponent class="w-1/2" />
        <AccountComponent class="md:hidden" />
      </div>
      <div class="my-4 md:flex md:w-[100%]">
        <SearchComponent
          placeholder="Recherchez un produit, un accord-cadre ou un fournisseur"
        >
          <i class="fa fa-search" />
        </SearchComponent>
      </div>
      <AccountComponent class="hidden md:flex" />
    </div>
    <MenuComponent class="hidden md:flex" />
  </nav>
</template>

<script lang="ts" setup>
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import SearchComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import MenuComponent from '@/vuejs/modules/shared/header-component/MenuComponent.vue'
import AccountComponent from '@/vuejs/modules/shared/header-component/AccountComponent.vue'
import LogoComponent from '@/vuejs/modules/shared/header-component/LogoComponent.vue'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const qantisLogoImg = getImage(qantisHeaderLogo)
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';

.menu-button {
  @apply z-50 flex rounded md:h-[50px] md:w-[220px];
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
