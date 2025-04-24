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
      <div v-if="product.variants?.length > 2" class="mx-auto items-center">
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
            @update-quantity="updateQuantity"
          />
        </div>
        <ButtonAddToCartComponent
          :product="product"
          :quantity="quantity"
          :variant-id="variantId"
          @click="
            $emit('click-add-cart', {
              partenaire_name: product.seller.name,
              partenaire_id: product.seller.id,
              product_name: product.name,
              product_id: product.id,
              qty_value: product.quantity,
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

import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'

import { formatPrice } from '@/vuejs/services/utils'

import { ProductPageList } from '@/vuejs/router/pages-list'

import { Product } from '@/vuejs/types/Product'
import { Variant } from '@/vuejs/types/Product/Variant'

const emit = defineEmits([
  'click-add-cart',
  'click-moins-qty',
  'click-plus-qty',
])

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const quantity = ref<number>(1)

const showLineThroughPrice = computed((): boolean => {
  return (
    props.product.priceReference &&
    props.product.priceReference !== props.product.price
  )
})

const variantId = computed((): string | null => {
  if (2 === props.product.variants?.length) {
    const variant = props.product.variants.filter(function (el: Variant) {
      return el.sku != null
    })
    return variant[0].id
  }
  return null
})

const productSlug = computed((): string => {
  return props.product.slug
})

const updateQuantity = (event) => {
  const eventName =
    quantity.value > event.quantity ? 'click-moins-qty' : 'click-plus-qty'
  emit(eventName)
  quantity.value = event.quantity
}
</script>
