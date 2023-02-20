<template>
  <div
    class="mx-auto flex flex-col h-[516px] w-[392px] justify-start rounded-md bg-white px-6 py-2"
  >
    <!-- Bloc header -->
    <div class="flex items-center justify-between h-[50px]">
      <div class="flex h-[40px] w-[40px] items-center justify-start loading rounded-md"></div>
      <div class="flex items-center justify-end">
        <button class="flex text-gray-500">
          <HeartIconComponent class="... stroke-gray-500" />
        </button>
      </div>
    </div>
    <!-- Fin bloc header -->

    <!-- Bloc image -->
    <div
      class="mx-auto flex h-auto w-full items-center justify-center rounded-lg h-[150px] loading px-6 py-2"
    >
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full h-[100px] flex-col justify-start loading px-6 py-2 mt-10 rounded-md">
    </div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div class="flex w-[150px] h-[60px] items-center justify-start xl:mt-2 loading px-6 py-2 rounded-md">
    </div>
    <!-- Fin bloc prix -->

    <!-- Bloc quantité -->
    <div class="flex justify-between xl:mt-5">
      <div class="flex items-center justify-start">
        <span class="text-sm text-gray-500">Qté: </span>
        <select class="rounded-md border border-gray-300">
          <option v-for="i in 5" :key="i" value="{{i}}">
            {{ i }}
          </option>
        </select>
      </div>
      <div class=" flex items-center justify-end">
        <ButtonComponent class="button-gradient">
          <ShoppingCartIconComponent class="w-4" /> Ajouter
        </ButtonComponent>
      </div>
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
</script>

<style scoped>
.loading {
  /*color: transparent;*/
  /*background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);*/
  /*background-size: 200% 100%;*/
  /*animation: 1s shine linear infinite;*/
  background: linear-gradient(-90deg, #e8e8e8, #d0cfcf, #e8e8e8);
  background-size: 300% 300%;
  animation: gradient 1s ease infinite;
}


@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
</style>
