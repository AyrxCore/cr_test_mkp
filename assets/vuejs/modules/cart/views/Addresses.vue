<template>
  <CartPage :selected-tab="tab">
    <template #left-side>
      <div class="">
        <h3 class="primary mb-1 text-[35px]">Adresses</h3>
      </div>
      <div class="w-full inline-flex bg-white p-5  rounded-md items-center">
        <label class="text-gray-500 flex-none">Référence de votre commande:</label>
        <input type="text" name="reference" class="rounded-md border border-gray-300 ml-2 w-full"/>
      </div>
      <div class="grid grid-cols-2 gap-2 mt-5">
        <div class="">
          <h3 class="primary mb-5 text-[25px]">
            Adresse de facturation
          </h3>
          <div class="text-gray-500 p-5 bg-white rounded-lg items-center mt-5">
            <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
          </div>
        </div>
        <div class="">
          <h3 class="primary mb-5 text-[25px]">
            Adresse de livraison
          </h3>
          <div class="text-gray-500 p-5 bg-white rounded-lg items-center mt-5">
            <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
            <p class="mt-5">
              <select class="h-[35px] w-full rounded-md text-gray-600 placeholder-gray-400 py-0 text-center" >
                <option>Choisir une autre adresse</option>
              </select>
            </p>
          </div>
          <DefaultButtonComponent
            :btn-text-color="'text-purple-500'"
            :btn-color="'bg-transparent'"
            :rounded="'rounded-full'"
            class="border border-purple-600 mt-10"
          >
            Ajouter une adresse
          </DefaultButtonComponent>
        </div>
      </div>
    </template>
    <template #right-side>
      <CartRightSideComponent class="mt-14">
        <template #title>
          Récapitulatif
        </template>
        <template #button-label>
          Passer la commande
        </template>
      </CartRightSideComponent>
      <div class="grid grid-cols-4 gap-x-16 mt-5">
        <div class="p-5 bg-white rounded-lg items-center m-auto h-14">
          <CbIconComponent />
        </div>
        <div class="p-5 bg-white rounded-lg items-center m-auto h-14">
          <SepaIconComponent />
        </div>
      </div>
    </template>
  </CartPage>
</template>
<script lang="ts" setup>

import CartPage from '@/vuejs/modules/cart/pages/CartPage.vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/DefaultButtonComponent.vue'
import CbIconComponent from '@/vuejs/modules/shared/icon/CbIconComponent.vue'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import {TAB_ADRESSES} from '@/vuejs/modules/cart'
import { computed } from 'vue'

const tab = computed(() => {
  return TAB_ADRESSES
})
</script>

<style scoped></style>
