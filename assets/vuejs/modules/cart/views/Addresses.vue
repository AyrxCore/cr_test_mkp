<template>
  <CartPage :selected-tab="tab">
    <template #left-side>
      <div class="">
        <h3 class="mb-1 text-[23px] text-primary md:text-[35px]">Adresses</h3>
      </div>
      <div
        class="flex w-full flex-col rounded-md bg-white p-5 md:flex-row md:items-center"
      >
        <label class="flex text-gray-500">Référence de votre commande:</label>
        <input
          type="text"
          name="reference"
          class="w-full rounded-md border border-gray-300 md:ml-2"
        />
      </div>
      <div class="mt-5 flex flex-col lg:grid lg:grid-cols-2 lg:gap-2">
        <div class="">
          <h3 class="mb-5 text-[19px] text-primary md:text-[25px]">
            Adresse de facturation
          </h3>
          <div class="mt-5 items-center rounded-lg bg-white p-5 text-gray-500">
            <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
          </div>
        </div>
        <div>
          <h3
            class="my-5 text-[19px] text-primary md:mb-5 md:text-[25px] lg:my-0"
          >
            Adresse de livraison
          </h3>
          <div class="mt-5 items-center rounded-lg bg-white p-5 text-gray-500">
            <span>QANTIS, 185 allée des Cyprès, 69760 LIMONEST, FRANCE</span>
            <p class="mt-5">
              <select
                class="h-[35px] w-full rounded-md py-0 text-center text-gray-600 placeholder-gray-400"
              >
                <option>Choisir une autre adresse</option>
              </select>
            </p>
          </div>
          <ButtonComponent
            class="button-secondary mt-5 w-full border border-secondary !bg-transparent text-base !text-secondary md:w-auto"
          >
            Ajouter une adresse
          </ButtonComponent>
        </div>
      </div>
    </template>
    <template #right-side>
      <CartRightSideComponent
        class="lg:mt-14"
        :next-url="'/app/cart/confirmation'"
      >
        <template #title> Récapitulatif </template>
        <template #button-label> Continuer </template>
      </CartRightSideComponent>
      <div class="mt-5 flex justify-start">
        <div class="mr-4 h-14 items-center rounded-lg bg-white p-5">
          <CbIconComponent />
        </div>
        <div class="h-14 items-center rounded-lg bg-white p-5">
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
import { TAB_ADRESSES } from '@/vuejs/modules/cart'
import { computed } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

const tab = computed(() => {
  return TAB_ADRESSES
})
</script>

<style scoped></style>
