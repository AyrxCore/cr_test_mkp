<template>
  <div
    class="mb-2 flex min-h-[490px] w-full flex-col items-center rounded-md bg-white p-4 md:mb-0"
  >
    <!-- Bloc header -->
    <div class="flex h-[50px] w-full items-center justify-between">
      <div
        class="flex h-[50px] w-[78px] items-center justify-start rounded-md bg-white"
      >
        <img
          :alt="product.seller.name"
          :src="product.seller.avatar"
          class="h-full w-full object-contain"
        />
      </div>
      <div class="flex items-center">
        <div
          v-if="showProductDiscount"
          :style="{
            color: betterTextColor('primary'),
          }"
          class="text-sm rounded-md bg-secondary px-2 py-1"
        >
          -{{ product.percent }}%
        </div>
        <div
          v-else-if="
            !productStore.isSellable(product) &&
            !product.percent &&
            product.productTopLabel
          "
          :style="{
            color: betterTextColor('primary'),
          }"
          class="rounded-md bg-secondary px-2 py-1 text-sm"
        >
          {{ product.productTopLabel }}
        </div>
        <!-- <AddFavoriteComponent
          v-if="product.variants?.length === 2"
          :favorites-product="product.favorites"
          :product-id="product.id"
          :product-name="product.name"
          :variant-id="variantId"
          class="ml-4"
        /> -->
      </div>
    </div>
    <!-- Fin bloc header -->

    <div class="flex h-full w-full flex-col items-stretch">
      <RouterLink
        :to="{
          name: ProductPageList.PRODUCT,
          params: { slug: productSlug },
        }"
        @click="
          sendGtmEvent('select_item', {
            ecommerce: {
              item_list_id: selectedCategoryId ?? selectedSellerId,
              items: formatProductGtmEvent([product]),
            },
          })
        "
      >
        <!-- Bloc image -->
        <div class="my-1 flex w-full items-center">
          <div
            class="mx-auto flex h-[200px] max-w-[200px] items-center justify-center rounded-lg"
          >
            <img
              v-if="product.images[0]"
              :alt="product.name"
              :src="getMediaUrl(product.images[0])"
              class="flex h-full w-full cursor-pointer items-center object-contain lg:max-w-max"
            />
            <div
              v-else
              class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
            />
          </div>
        </div>
      </RouterLink>
      <!-- Fin bloc image -->

      <!-- Bloc texte -->
      <div class="flex h-3/5 w-full flex-col justify-between items-stretch">
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCT,
            params: { slug: productSlug },
          }"
          class="contents"
          @click="
            sendGtmEvent('select_item', {
              ecommerce: {
                item_list_id: selectedCategoryId ?? selectedSellerId,
                items: formatProductGtmEvent([product]),
              },
            })
          "
        >
          <!-- Bloc titre -->
          <div class="h-[25%]">
            <h3
              class="truncate-custom truncate-custom-2 text-left text-sm font-bold text-primary md:text-base lg:text-lg"
            >
              {{ product.name }}
            </h3>
          </div>
          <!-- Fin bloc titre -->

          <!-- Bloc nom partenaire -->
          <div class="mt-1 h-[10%] text-primary">
            {{ product.seller.name }}
          </div>
          <!-- Fin bloc nom partenaire -->
        </RouterLink>
        <div v-if="visibleTags.length" class="mt-1 flex flex-wrap gap-2">
          <ProductTagComponent v-for="tag in visibleTags" :key="tag.key" :tag="tag" compact />
        </div>
        <NotSellableProductCardButtonComponent
          v-if="!productStore.isSellable(product)"
          :product="product"
        />
        <ProductCardButtonsComponent v-else :product="product" />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { storeToRefs } from 'pinia'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { useProductStore } from '@/vuejs/stores/product'
import { getMediaUrl, betterTextColor } from '@/vuejs/services/utils'
import { formatProductGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { Product } from '@/vuejs/types/Product'
// import { Variant } from '@/vuejs/types/Product/Variant'

// import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import ProductCardButtonsComponent from '@/vuejs/modules/products/components/ProductCardButtonsComponent.vue'
import NotSellableProductCardButtonComponent from '@/vuejs/modules/products/components/NotSellableProductCardButtonComponent.vue'
import ProductTagComponent from '@/vuejs/modules/products/components/ProductTagComponent.vue'
import { useProductTags } from '@/vuejs/modules/products/composables/useProductTags'

const productStore = useProductStore()
const { selectedCategoryId, selectedSellerId } = storeToRefs(productStore)

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

// const variantId = computed((): string | null => {
//   if (2 === props.product.variants?.length) {
//     const variant = props.product.variants.filter(function (el: Variant) {
//       return el.externalId != null
//     })
//     return variant[0].id
//   }
//   return null
// })

const productSlug = computed((): string => {
  return props.product.slug
})

const showProductDiscount = computed((): boolean => {
  return props.product.percent > 0
})

const { visibleTags } = useProductTags(computed(() => props.product.tags), 'card')
</script>
