<template>
  <div
    class="mx-auto grid h-[516px] w-[392px] items-center justify-start rounded-md bg-white px-6 py-2"
  >
    <!-- Bloc header -->
    <div class="flex w-full items-center justify-between">
      <div class="flex h-[50px] items-center justify-start bg-white">
        <img
          :src="props.product.company.avatar"
          :alt="props.product.company.name"
          class="flex "
        />
      </div>
      <div class="flex items-center justify-end">
        <button class="flex text-gray-500">
          <HeartIconComponent class="... stroke-gray-500" />
        </button>
      </div>
    </div>
    <!-- Fin bloc header -->

    <!-- Bloc image -->
    <div
      class="mx-auto flex h-auto w-full items-center justify-center rounded-lg"
    >
      <img
        :src="props.product.images[0]"
        :alt="props.product.name"
        class="flex"
      />
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full flex-col justify-start">
      <h3
        class="text-left text-sm font-bold text-gray-600 md:text-base lg:text-lg"
      >
        <RouterLink :to="{ path: `/app/product/${props.product.id}` }">{{ props.product.name }}</RouterLink>
      </h3>
      <p
        class="mt-1 w-full justify-start text-left text-sm text-gray-400 md:text-base lg:text-lg"
        v-html="props.product.description"
       />
    </div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div class="flex w-full items-center justify-start xl:mt-2">
      <span
        v-if="props.product.price?.displayPrice"
        class="mr-2 text-sm font-bold text-primary md:text-base lg:text-lg"
        >{{ props.product.price?.displayPrice }}€</span
      >
      <span
        v-if="showLineThroughPrice"
        :class="{
          'line-through text-sm text-gray-500  md:text-base lg:text-lg': product.price?.displayPrice,
          'text-sm md:text-base lg:text-lg font-bold text-primary': product.price === null
        }"
        >
          {{ props.product.priceReference }}€ HT
      </span>
    </div>
    <!-- Fin bloc prix -->

    <!-- Bloc quantité -->
    <div class="mx-auto flex w-full justify-between xl:mt-5">
      <div class="justify-end">
        <span class="text-sm text-gray-500">Qté: </span>
        <select class="rounded-md border border-gray-300">
          <option v-for="i in 5" :key="i" value="{{i}}">
            {{ i }}
          </option>
        </select>
      </div>
      <ButtonComponent class="button-gradient" :is-loading="isLoading">
        <ShoppingCartIconComponent class="w-4" /> Ajouter
      </ButtonComponent>
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import {Product } from '@/vuejs/types/Product'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})
const isLoading = ref<boolean>(false)

const showLineThroughPrice = computed(() => {
  return props.product.priceReference && props.product.priceReference !== props.product.price?.displayPrice
})
</script>

<style scoped></style>
