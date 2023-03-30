<template>
  <div
    class="sticky bottom-1 flex w-full items-center border bg-white p-4 lg:relative lg:bottom-0 lg:border-0 lg:bg-transparent"
  >
    <div class="flex w-full flex-col">
      <div
        v-if="props.showPrice"
        class="mb-2 flex flex-row items-center justify-between md:justify-evenly lg:hidden"
      >
        <div
          v-if="props.priceReference"
          class="flex items-center justify-center text-center"
        >
          <span class="text-lg text-gray-500 line-through"
          >{{ props.priceReference }}€ HT
          </span>
        </div>
        <div
          v-if="props.price"
          class="flex items-center justify-center text-[22px] font-bold text-primary md:text-[25px]"
        >
          {{ props.price }}€ HT
        </div>
        <div class="mt-3 ml-2 flex">
          <span
            v-if="props.percent > 0"
            class="ml-2 rounded-lg bg-purple-600 px-2.5 py-1.5 text-sm text-white md:text-lg"
          >{{ props.percent }} %</span
          >
        </div>
      </div>
      <div class="flex flex-row">
        <ButtonAddToCartComponent
          :product="props.product"
          :quantity="props.quantity"
          :variant-id="props.variantId"
          :price="props.price"
          class="w-full lg:mt-14"
        />
        <!-- <button class="ml-5 items-center rounded-lg border lg:hidden">
          <HeartIconComponent
            class="m-2 flex h-8 w-8 lg:hidden"
            :stroke-color="'#5E6875'"
          />
        </button> -->
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import { PropType } from 'vue'
import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
  showPrice: {
    required: false,
    type: Boolean,
    default: false,
  },
  quantity: {
    required: false,
    type: Number,
    default: 1,
  },
  variantId: {
    required: false,
    type: Number,
    default: null,
  },
  price: {
    required: false,
    type: Number,
    default: null,
  },
  priceReference: {
    required: false,
    type: Number,
    default: null,
  },
  percent: {
    required: false,
    type: Number,
    default: null,
  },
})
</script>

<style scoped></style>
