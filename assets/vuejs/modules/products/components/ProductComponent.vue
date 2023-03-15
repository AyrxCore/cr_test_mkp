<template>
  <div
    class="mx-auto flex h-[466px] w-[392px] flex-col items-center justify-start rounded-md bg-white px-6 py-4 lg:h-[516px]"
  >
    <!-- Bloc header -->
    <div class="flex h-[50px] w-full items-center justify-between">
      <div
        class="flex h-[50px] w-[78px] items-center justify-start rounded-md bg-white"
      >
        <img
          :src="getUpplerImage(props.product.seller.avatar)"
          :alt="props.product.seller.name"
          class="h-full w-full object-contain"
        />
      </div>
      <!-- <div class="flex items-center justify-end">
        <button class="flex text-gray-500">
          <HeartIconComponent class="... stroke-gray-500" />
        </button>
      </div> -->
    </div>
    <!-- Fin bloc header -->

    <!-- Bloc image -->
    <div
      class="mx-auto flex h-[139px] w-full items-center justify-center rounded-lg px-1 lg:h-[191px]"
    >
      <img
        :src="getUpplerImage(props.product.images[0])"
        :alt="props.product.name"
        class="flex h-full w-full max-w-max cursor-pointer items-center"
        @click="
          $router.push({
            name: ProductPageList.PRODUCT,
            params: { id: props.product.id },
          })
        "
      />
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full flex-col justify-start">
      <h3
        class="truncate-custom truncate-custom-2 h-[55px] text-left text-sm font-bold text-gray-600 md:text-base lg:text-lg"
      >
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCT,
            params: { id: props.product.id },
          }"
          >{{ props.product.name }}</RouterLink
        >
      </h3>
      <div class="h-[100px]">
        <p
          class="truncate-custom truncate-custom-3 mt-1 w-full justify-start text-left text-sm text-gray-400 md:text-base lg:text-lg"
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
          'text-sm text-gray-500 line-through md:text-base lg:text-lg':
            product.price?.displayPrice,
          'text-sm font-bold text-primary md:text-base lg:text-lg':
            product.price === null,
        }"
      >
        {{ formatPrice(props.product.priceReference) }}€ HT
      </span>
    </div>
    <!-- Fin bloc prix -->

    <!-- Bloc quantité -->
    <div class="flex w-full justify-between">
      <div class="flex items-center justify-start">
        <span class="mr-2 text-sm text-gray-500">Qté :</span>
        <select v-model="quantity" class="rounded-md border border-gray-300">
          <option v-for="i in 5" :key="i" value="{{i}}">
            {{ i }}
          </option>
        </select>
      </div>
      <ButtonAddToCartComponent :product="props.product" :quantity="quantity" />
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'

import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'

import { formatPrice } from '@/vuejs/services/utils'
import { Product } from '@/vuejs/types/Product'
import { getUpplerImage } from '@/vuejs/services/utils'
import { ProductPageList } from '@/vuejs/router/pages-list'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const quantity = ref<number>(1)

const showLineThroughPrice = computed(() => {
  return (
    props.product.priceReference &&
    props.product.priceReference !== props.product.price?.displayPrice
  )
})
</script>

<style scoped></style>
