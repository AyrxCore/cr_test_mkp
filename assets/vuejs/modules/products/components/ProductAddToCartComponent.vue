<template>
  <div
    :class="{
      'bottom-[400px]! lg:bottom-1': tooltipFavoriteIsOpened,
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
          :disabled="disabled"
          :product="product"
          :quantity="product.quantity"
          @click="
            sendGtmEvent('add_to_cart', {
              ecommerce: {
                currency: 'EUR',
                value: product.price * product.quantity,
                items: formatProductGtmEvent([product]),
              },
            })
          "
        />
        <!-- <AddFavoriteComponent
          :favorites-product="product.favorites"
          :offer-price-id="offerPriceExternalId"
          :product-id="product.id"
          :product-name="product.name"
          class="ml-5 lg:hidden"
          @toggle-favorite="onToggleFavoriteTooltip"
        /> -->
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType, ref } from 'vue'

import { formatProductGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { Product } from '@/vuejs/types/Product'

import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
  showPrice: {
    required: false,
    type: Boolean,
    default: false,
  },
  disabled: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const tooltipFavoriteIsOpened = ref<boolean>(false)
</script>
