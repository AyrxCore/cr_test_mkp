<template>
  <div
    class="mb-2 flex min-h-[490px] flex-col items-center rounded-md bg-white p-4 md:mb-0"
  >
    <!-- Bloc header -->
    <div class="flex h-[50px] w-full items-center justify-between">
      <div
        class="flex h-[50px] w-[78px] items-center justify-start rounded-md bg-white"
      >
        <img
          :alt="product.seller.name"
          :src="getUpplerImage(product.seller.avatar)"
          class="h-full w-full object-contain"
        />
      </div>
      <div class="flex items-center">
        <div
          v-if="showProductDiscount"
          :style="{
            color: betterTextColor('primary'),
          }"
          class="text-md rounded-md bg-secondary px-2 py-1"
        >
          -{{ product.percent }}%
        </div>
        <div
          v-else-if="!product.sellable && !product.percent"
          :style="{
            color: betterTextColor('primary'),
          }"
          class="text-md rounded-md bg-secondary px-2 py-1"
        >
          Offre sur-mesure
        </div>
        <AddFavoriteComponent
          v-if="product.variants?.length === 2"
          :favorites-product="product.favorites"
          :product-id="product.id"
          :product-name="product.name"
          :variant-id="variantId"
          class="ml-4"
        />
      </div>
    </div>
    <!-- Fin bloc header -->

    <div class="flex h-full w-full flex-col items-center">
      <!-- Bloc image -->
      <div class="my-1 flex w-full items-center">
        <div
          class="mx-auto flex h-[200px] max-w-[200px] items-center justify-center rounded-lg"
        >
          <img
            v-if="product.images[0]"
            :alt="product.name"
            :src="getUpplerImage(product.images[0])"
            class="flex h-full w-full cursor-pointer items-center object-contain lg:max-w-max"
            @click="goToProductPage"
          />
          <div
            v-else
            class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
          />
        </div>
      </div>
      <!-- Fin bloc image -->

      <!-- Bloc texte -->
      <div class="flex h-3/5 w-full flex-col justify-between">
        <!-- Bloc titre -->
        <div class="h-[25%]">
          <h3
            class="truncate-custom truncate-custom-2 text-left text-sm font-bold text-primary md:text-base lg:text-lg"
          >
            <RouterLink
              :to="{
                name: ProductPageList.PRODUCT,
                params: { slug: productSlug },
              }"
              @click="sendGAEventData('click-title')"
            >
              {{ product.name }}
            </RouterLink>
          </h3>
        </div>
        <!-- Fin bloc titre -->

        <!-- Bloc nom partenaire -->
        <div class="mt-1 h-[10%] text-primary">
          {{ product.seller.name }}
        </div>

        <!-- Fin bloc nom partenaire -->

        <!-- Bloc description -->
        <div class="h-[35%]">
          <p
            class="truncate-custom truncate-custom-3 mt-1 w-full justify-start text-left text-sm md:text-base lg:text-lg"
            v-html="productDescription"
          />
        </div>
        <!-- Fin bloc description -->
        <NotSellableProductCardButtonComponent
          v-if="!product.sellable"
          :product="product"
        />
        <ProductCardButtonsComponent
          v-else
          :product="product"
          @click-add-cart="sendGAEventData('click-add-cart')"
          @click-plus-qty="sendGAEventData('click-plus-qty')"
          @click-moins-qty="sendGAEventData('click-moins-qty')"
        />
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'

import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import ProductCardButtonsComponent from '@/vuejs/modules/products/components/ProductCardButtonsComponent.vue'
import NotSellableProductCardButtonComponent from '@/vuejs/modules/products/components/NotSellableProductCardButtonComponent.vue'

import { getUpplerImage, betterTextColor } from '@/vuejs/services/utils'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'

import { Product } from '@/vuejs/types/Product'
import { Variant } from '@/vuejs/types/Product/Variant'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const emit = defineEmits([
  'click-add-cart',
  'click-title',
  'click-img',
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

const variantId = computed((): number | null => {
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

const showProductDiscount = computed((): boolean => {
  return props.product.percent > 0
})

const productDescription = computed((): string => {
  if (props.product.description?.length > 140) {
    return props.product.description.substring(0, 140) + '...'
  }
  return props.product.description
})

const goToProductPage = () => {
  router.push({
    name: ProductPageList.PRODUCT,
    params: { slug: productSlug.value },
  })
  sendGAEventData('click-img')
}

const sendGAEventData = (eventName: string) => {
  sendGaEvent(eventName, {
    partenaire_name: props.product.seller.name,
    partenaire_id: props.product.seller.id,
    product_name: props.product.name,
    product_id: props.product.id,
    qty_value: quantity.value,
  })
}
</script>
