<template>
  <div
    class="sticky bottom-1 flex w-full items-center border bg-white lg:relative lg:bottom-0 lg:border-0 lg:bg-transparent"
    :class="{
      '!bottom-[400px] lg:bottom-1': tooltipFavoriteIsOpened,
      'p-4': showPrice,
    }"
  >
    <div class="flex w-full flex-col">
      <div
        v-if="props.showPrice"
        class="mb-2 flex flex-row items-center justify-between md:justify-evenly lg:hidden"
      >
        <div class="flex items-center justify-center text-center">
          <span
            v-if="props.product.priceReference"
            class="text-lg text-gray-500 line-through"
            >{{ props.product.priceReference }}€ HT
          </span>
        </div>
        <div
          v-if="props.product.price"
          class="flex items-center justify-center text-[22px] font-bold text-primary md:text-[25px]"
        >
          {{ props.product.price }}€ HT
        </div>
        <div class="mt-3 ml-2 flex">
          <span
            v-if="props.product.percent > 0"
            class="ml-2 rounded-lg bg-purple-600 px-2.5 py-1.5 text-sm text-white md:text-lg"
            >{{ props.product.percent }} %</span
          >
        </div>
      </div>
      <div class="flex flex-row">
        <ButtonAddToCartComponent
          :product="props.product"
          :quantity="props.product.quantity"
          :variant-id="props.product.defaultVariantId"
          :price="props.product.price"
          :class="{ 'w-full': showPrice }"
        />
        <AddFavoriteComponent
          class="ml-5 lg:hidden"
          :product-id="props.product.id"
          :product-name="props.product.name"
          :variant-id="props.product.defaultVariantId"
          :favorites-product="props.product.favorites"
          @open-favorite="onOpenFavoriteTooltip"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import { PropType, ref } from 'vue'
import { Product } from '@/vuejs/types/Product'
import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'

const tooltipFavoriteIsOpened = ref(false)
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
})

const onOpenFavoriteTooltip = (event) => {
  tooltipFavoriteIsOpened.value = event.showTooltip
}
</script>

<style scoped></style>
