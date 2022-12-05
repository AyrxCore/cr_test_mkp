<template>
  <div
    class="text-cotext mx-auto h-full items-center rounded-md bg-white px-6 py-2"
  >
    <div class="flex items-center justify-between">
      <div class="h-9 w-20 bg-white">
        <img :src="props.product.imgFrn" alt="Image produit" />
      </div>
      <div class="border"></div>
      <HeartIconComponent />
    </div>
    <div class="mx-auto rounded-lg flex justify-center items-center h-[191px!important]">
      <a href="/app/product">
        <img
          :src="props.product.imgProduct"
          alt="Image produit"
          class="h-[auto!important]"
        />
      </a>
    </div>
    <h3 class="text-cotext text-left text-lg font-bold text-gray-600">
      <a href="/app/product">Nom du produit</a>
    </h3>
    <p class="text-cotext mt-1 text-left text-lg text-gray-400">
      Description du produit ou du service
    </p>
    <div class="mt-2 flex items-center">
      <span class="primary mr-2 text-lg font-bold">85,50€</span>
      <span class="text-lg text-gray-400 line-through">100,00€ HT</span>
    </div>
    <div class="mx-auto mt-5 flex justify-between">
      <div>
        <span class="primary text-sm text-gray-300">Qté: </span>
        <select class="rounded-md border border-gray-300">
          <option v-for="i in 5" :key="i" value="{{i}}">
            {{ i }}
          </option>
        </select>
      </div>
      <DefaultButtonComponent type="submit" :is-loading="isLoading">
        <ShoppingCartIconComponent class="mr-2 w-4" /> Ajouter
      </DefaultButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import DefaultButtonComponent from '@/vuejs/modules/shared/GradientButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})

const isLoading = ref<boolean>(false)
</script>

<style scoped></style>
