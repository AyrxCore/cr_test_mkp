<template>
  <nav class="sticky top-0 z-30 w-full bg-primary px-4 py-1 lg:pt-5 lg:pb-3">
    <div class="mx-auto items-center px-4 lg:flex">
      <div class="flex justify-between">
        <MenuComponent class="lg:hidden" />
        <LogoComponent class="w-1/2" />
        <AccountComponent class="lg:hidden" />
      </div>
      <div class="mt-4 mb-2 lg:my-4 lg:flex lg:w-[100%]">
        <SearchComponent @search-product="search"/>
      </div>
      <AccountComponent class="hidden lg:flex" />
    </div>
    <MenuComponent class="hidden lg:flex" />
  </nav>
</template>

<script lang="ts" setup>
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import MenuComponent from '@/vuejs/modules/shared/header-component/MenuComponent.vue'
import AccountComponent from '@/vuejs/modules/shared/header-component/AccountComponent.vue'
import LogoComponent from '@/vuejs/modules/shared/header-component/LogoComponent.vue'
import SearchComponent from '@/vuejs/modules/shared/header-component/SearchComponent.vue'
import router from '@/vuejs/router';
import { ProductPageList } from '@/vuejs/modules/products/routerProducts';

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const search = ((event) => {
  router.push({
    name: ProductPageList.PRODUCTS,
    query: {q: event.term}
  })
})
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';

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
