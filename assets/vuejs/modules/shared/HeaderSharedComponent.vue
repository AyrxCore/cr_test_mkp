<template>
  <nav class="sticky top-0 z-30 w-full bg-white py-1 shadow-md lg:pb-3">
    <div class="mx-auto items-center px-4 lg:flex">
      <div class="flex justify-between">
        <MenuComponent class="lg:hidden" />
        <LogoComponent />
        <div class="mr-4 flex items-center justify-end lg:hidden">
          <StoreLocHeaderButton />
          <AccountComponent class="ml-4" />
          <CartButtonComponent class="ml-4" />
        </div>
      </div>
      <div class="mb-2 mt-4 lg:my-4 lg:w-[100%]">
        <SearchComponent @search-product="search" />
      </div>
      <div
        class="hidden items-center lg:ml-4 lg:flex lg:min-w-[400px] lg:justify-evenly xl:ml-0 xl:min-w-[800px]"
      >
        <div class="hidden xl:flex">
          <ContactUsButtonComponent />
        </div>
        <StoreLocHeaderButton />
        <AccountComponent />
        <CartButtonComponent class="lg:ml-4 xl:ml-0" />
      </div>
    </div>
    <MenuComponent :icon-color="channelSecondaryColor" class="hidden lg:flex" />
  </nav>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import MenuComponent from '@/vuejs/modules/shared/header-component/MenuComponent.vue'
import AccountComponent from '@/vuejs/modules/shared/header-component/AccountComponent.vue'
import LogoComponent from '@/vuejs/modules/shared/header-component/LogoComponent.vue'
import SearchComponent from '@/vuejs/modules/shared/header-component/SearchComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import CartButtonComponent from '@/vuejs/modules/shared/CartButtonComponent.vue'
import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useProductStore } from '@/vuejs/stores/product'
import { useChannelStore } from '@/vuejs/stores/channel'
import MarkerIconComponent from '@/vuejs/modules/shared/icon/MarkerIconComponent.vue'
import StoreLocHeaderButton from '@/vuejs/modules/shared/StoreLocHeaderButton.vue'

const productStore = useProductStore()

const { channelSecondaryColor } = storeToRefs(useChannelStore())

const search = (event) => {
  productStore.setSelectedProperty(null)
  productStore.setSelectedCategory(null)
  productStore.setSelectedCompany(null)
  router.push({
    name: ProductPageList.PRODUCTS,
    query: {
      q: event.term,
    },
  })
}
</script>

<style lang="scss">
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
      color: #000000;
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
