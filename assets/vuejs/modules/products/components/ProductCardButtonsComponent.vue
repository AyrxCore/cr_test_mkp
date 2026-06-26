<template>
  <!-- Bloc prix -->
  <div class="my-1 flex h-[10%] w-full items-center justify-start">
    <span
      v-if="product.price"
      class="mr-2 text-lg font-bold text-primary md:text-base lg:text-lg"
    >
      {{ formatPrice(product.price) }}€
    </span>
    <span
      v-if="showLineThroughPrice"
      :class="{
        'text-sm line-through': product.price,
        'text-sm font-bold': product.price === null,
      }"
    >
      {{ formatPrice(product.priceReference) }}€ HT
    </span>
  </div>
  <!-- Fin bloc prix -->

  <!-- Bloc quantité -->
  <div class="mb-1 flex h-[20%] justify-end">
    <div class="mt-1 flex w-full justify-between">
      <div v-if="hasOptions" class="mx-auto items-center">
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCT,
            params: { slug: productSlug },
          }"
          class="button border-2 border-primary !text-primary shadow-none hover:scale-105 hover:!border-primary hover:!bg-white hover:!shadow-inner-darker focus:!bg-white"
        >
          Voir les options
          <ArrowRightIconComponent class="ml-2 w-4 stroke-primary" />
        </RouterLink>
      </div>

      <div v-else class="flex w-full justify-between">
        <div class="flex items-center justify-start">
          <ProductQuantityComponent
            :quantity="quantity"
            :min-quantity="product.minOrderQuantity ?? 1"
            :max-quantity="product.maxOrderQuantity ?? 999"
            @update-quantity="updateQuantity"
          />
        </div>
        <ButtonAddToCartComponent
          :product="product"
          :quantity="quantity"
          @click="
            sendGtmEvent('add_to_cart', {
              ecommerce: {
                currency: 'EUR',
                value: product.price * quantity,
                items: formatProductGtmEvent([product]),
              },
            })
          "
        />
      </div>
    </div>
  </div>
  <!-- Fin bloc quantité -->
</template>

<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { formatPrice } from '@/vuejs/services/utils'
import { formatProductGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { Product } from '@/vuejs/types/Product'

import { OPTION_PROPERTY_NAMES } from '@/vuejs/constants/productOptionProperties'

import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const quantity = ref<number>(props.product.minOrderQuantity ?? 1)

const hasOptions = computed((): boolean => {
  const keys = Object.keys(props.product.properties ?? {}).map((k) => k.toLowerCase())
  return OPTION_PROPERTY_NAMES.some((name) => keys.includes(name))
})

const productSlug = computed((): string => props.product.slug)

const showLineThroughPrice = computed((): boolean => {
  return (
    props.product.priceReference &&
    props.product.priceReference !== props.product.price
  )
})

const updateQuantity = (event) => {
  quantity.value = event.quantity
}
</script>
