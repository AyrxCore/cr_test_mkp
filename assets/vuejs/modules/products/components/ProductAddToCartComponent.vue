<template>
  <div
    :class="{
      '!bottom-[400px] lg:bottom-1': tooltipFavoriteIsOpened,
      'p-4': showPrice,
    }"
    class="sticky bottom-1 flex w-full items-center border bg-white lg:relative lg:bottom-0 lg:border-0 lg:bg-transparent"
  >
    <div class="flex w-full flex-col">
      <div
        v-if="showPrice"
        class="mb-2 flex flex-row items-center justify-between md:justify-evenly lg:hidden"
      >
        <div class="flex items-center justify-center text-center">
          <span
            v-if="product.priceReference"
            class="text-lg text-gray-500 line-through"
            >{{ product.priceReference }}€ HT
          </span>
        </div>
        <div
          v-if="product.price"
          class="flex items-center justify-center text-[22px] font-bold text-primary md:text-[25px]"
        >
          {{ product.price }}€ HT
        </div>
        <div class="ml-2 mt-3 flex">
          <span
            v-if="product.percent > 0"
            class="ml-2 rounded-lg bg-purple-600 px-2.5 py-1.5 text-sm text-white md:text-lg"
            >{{ product.percent }} %</span
          >
        </div>
      </div>
      <div class="flex flex-row">
        <ButtonAddToCartComponent
          :class="{ 'w-full': showPrice }"
          :price="product.price"
          :product="product"
          :quantity="product.quantity"
          :variant-id="product.defaultVariantId"
          @click="
            sendGaEvent('click_product_add_cart', {
              product_name: product.name,
            })
          "
        />
        <AddFavoriteComponent
          :favorites-product="product.favorites"
          :product-id="product.id"
          :product-name="product.name"
          :variant-id="product.defaultVariantId"
          class="ml-5 lg:hidden"
          @toggle-favorite="onToggleFavoriteTooltip"
          @open-favorite="
            sendGaEvent('click_product_favorite', {
              product_name: product.name,
            })
          "
          @select-favorite="
            sendGaEvent('click_product_select_favorite_list', {
              product_name: product.name,
              favorite_list_name: $event,
            })
          "
          @add-favorite-list="
            sendGaEvent('type_product_favorite_list', {
              product_name: product.name,
              favorite_list_name: $event,
            })
          "
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
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

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

const onToggleFavoriteTooltip = (event) => {
  tooltipFavoriteIsOpened.value = event.showTooltip
}
</script>

<style scoped></style>
