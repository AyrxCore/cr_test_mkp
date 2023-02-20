<template>
  <div
    class="mx-auto flex flex-col h-[466px] lg:h-[516px] w-[392px] items-center justify-start rounded-md bg-white px-6 py-2"
  >
    <!-- Bloc header -->
    <div class="flex items-center justify-between h-[50px] w-full">
      <div class="flex w-[78px] h-[37px] items-center justify-start bg-white rounded-md">
        <img
          :src="getUpplerImage(props.product.seller.avatar)"
          :alt="props.product.seller.name"
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
      class="mx-auto flex w-full items-center justify-center rounded-lg px-1 h-[139px] lg:h-[191px]"
    >
      <img
        :src="getUpplerImage(props.product.images[0])"
        :alt="props.product.name"
        class="flex items-center w-full h-full max-w-max"
      />
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full flex-col justify-start">
      <h3
        class="text-left text-sm font-bold text-gray-600 md:text-base lg:text-lg h-[40px]"
      >
        <RouterLink :to="{ path: `/app/product/${props.product.id}` }">{{ props.product.name }}</RouterLink>
      </h3>
      <div class="h-[100px] mt-2">
        <p
          class="mt-1 w-full justify-start text-left text-sm text-gray-400 md:text-base lg:text-lg"
          v-html="props.product.description"
        />
      </div>

    </div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div class="flex w-full items-center justify-start xl:mt-1">
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
    <div class="flex justify-between mt-5 w-full">
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
import { computed, PropType, ref } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import {Product } from '@/vuejs/types/Product'
import { getUpplerImage } from '@/vuejs/services/utils'

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
