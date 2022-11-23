<template>
  <nav class="header sticky top-0 z-30 bg-white py-0.5 px-4">
    <div class="mx-auto w-[100%] px-4 md:flex md:items-center">
      <div class="md:w-[10%] mt-1">
        <a href="#" class="text-xl font-bold text-white">
          <img
            :src="qantisLogoImg"
            alt="header"
            class="logo-qantis-header sm:mx-auto sm:mb-2"
          />
        </a>
      </div>
      <div
        class="mx-auto mr-10 ml-5 h-[55px] min-w-max grow flex-row md:flex md:w-[100%]"
      >
        <SearchComponent placeholder="Recherchez un produit, un accord-cadre ou un fournisseur">
          <i class="fa fa-search" />
        </SearchComponent>
      </div>
      <div
        id="navbar-collapse"
        class="w-[20%] items-center justify-end text-white md:flex mt-3"
      >
        <AccountComponent />
        <div class="ml-3 flex text-white">
          <div class="mr-4 flex">
            <a href="#">
              <HeartIconComponent />
            </a>
          </div>
          <div class="flex">
            <a href="#">
              <ShoppingCartIconComponent />
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
import SearchComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import MenuComponent from '@/vuejs/modules/shared/MenuComponent.vue'
import AccountComponent from '@/vuejs/modules/shared/AccountComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue';
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue';

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const qantisLogoImg = getImage(qantisHeaderLogo)
</script>

<style lang="scss">
@import 'assets/style/_variables.scss';
.logo-qantis-header {
  height: 57px;
  width: 145px;
  left: 60px;
  top: 24.5px;
  border-radius: 0px;

}


.menu-button {
  width: 220px;
  height: 50px;
  border-radius: 5px;
  z-index: 200;
  font-family: CoText, sans-serif;
}


.overlay {
  display: none;
  height: 100vh;
  width: 100%;
  position: fixed;
  z-index: 99;
  opacity: 0;
  background-color: rgba(0,0,0,0.4);
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
      display: block;
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
