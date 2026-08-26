<template>
  <div class="min-w-0 overflow-x-hidden rounded-2xl bg-white p-4 shadow-sm md:p-6">
    <div class="mb-4">
      <h4 class="text-lg font-semibold text-primary">
        Produits
        <span class="ml-1 text-sm font-normal text-gray-500 md:hidden"
          >({{ count }})</span
        >
        <span class="ml-1 hidden text-sm font-normal text-gray-500 md:inline"
          >({{ count }} résultat{{ count > 1 ? 's' : '' }})</span
        >
      </h4>
    </div>

    <div
      class="grid min-w-0 grid-cols-1 items-stretch justify-items-center gap-3 md:grid-cols-2 md:gap-5 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"
    >
      <a
        v-if="bannerSearchImg && bannerSearchLink && allowBannerSearch"
        :class="products.length < 5 ? 'row-start-1' : 'row-start-2'"
        :href="bannerSearchLink ?? ''"
        class="col-span-1 mt-9 h-[450px] w-full md:col-span-2 md:mt-0 xl:h-full"
        target="_blank"
        @click="$emit('banner-click', bannerSearchLink!)"
      >
        <div
          :style="{
            backgroundImage: 'url(' + bannerSearchImg + ')',
            backgroundPosition: 'center',
            backgroundSize: 'cover',
          }"
          class="h-full w-full rounded-lg"
        />
      </a>

      <ProductCardComponent
        v-for="product in products"
        :key="`p-${product.id}`"
        :product="product"
        class="mt-3 h-full! w-full! rounded-lg border border-gray-300 bg-white shadow-sm md:mt-0 md:max-w-[350px]"
      />
    </div>

    <div v-if="hasMore" class="mt-5 flex w-full justify-center">
      <ButtonComponent
        class="button button-primary-outline w-full md:w-1/2"
        @click="$emit('load-more')"
      >
        <LoaderSharedComponent v-if="loadMoreLoading" />
        <span v-else class="text-lg!">Charger plus de produits</span>
      </ButtonComponent>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { Product } from '@/vuejs/types/Product'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ProductCardComponent from '@/vuejs/modules/products/components/ProductCardComponent.vue'

defineProps<{
  products: Product[]
  count: number
  hasMore: boolean
  loadMoreLoading: boolean
  bannerSearchImg: string | null
  bannerSearchLink: string | null
  allowBannerSearch: boolean
}>()

defineEmits<{
  'load-more': []
  'banner-click': [link: string]
}>()
</script>
