<template>
  <div
    class="mx-auto flex h-[516px] w-[392px] flex-col justify-start rounded-md bg-white px-6 py-2"
  >
    <!-- Bloc header -->
    <div class="flex h-[50px] items-center justify-between">
      <div
        class="loading flex h-[40px] w-[40px] items-center justify-start rounded-md"
      ></div>
    </div>
    <!-- Fin bloc header -->

    <!-- Bloc image -->
    <div
      class="loading mx-auto flex h-auto h-[150px] w-full items-center justify-center rounded-lg px-6 py-2"
    ></div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div
      class="loading mt-10 flex h-[100px] w-full flex-col justify-start rounded-md px-6 py-2"
    ></div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div
      class="loading flex h-[60px] w-[150px] items-center justify-start rounded-md px-6 py-2 xl:mt-2"
    ></div>
    <!-- Fin bloc prix -->

    <!-- Bloc quantité -->
    <div class="flex justify-between xl:mt-5">
      <div class="flex items-center justify-start">
        <span class="mr-2 text-sm text-gray-500">Qté: </span>
        <ProductQuantityComponent quantity="1" />
      </div>
      <div class="flex items-center justify-end">
        <ButtonComponent class="button-gradient">
          <ShoppingCartIconComponent class="w-4" />
          Ajouter
        </ButtonComponent>
      </div>
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
</script>
