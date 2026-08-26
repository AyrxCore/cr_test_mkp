<template>
  <div class="mt-6 flex h-[30%] items-end justify-center">
    <div class="mx-auto items-center">
      <RouterLink
        :to="{
          name: ProductPageList.PRODUCT,
          params: { slug: productSlug },
        }"
        class="button border-2 border-primary text-primary! shadow-none hover:scale-105 hover:border-primary! hover:bg-white! hover:shadow-inner-darker! focus:bg-white!"
      >
        Profiter de l'offre
        <ArrowRightIconComponent class="ml-2 w-4 stroke-primary" />
      </RouterLink>
    </div>
  </div>
  <!-- Fin bloc texte -->
</template>
<script lang="ts" setup>
import { computed, PropType } from 'vue'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'

import { Product } from '@/vuejs/types/Product'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const productSlug = computed((): string => {
  return props.product.slug
})
</script>
