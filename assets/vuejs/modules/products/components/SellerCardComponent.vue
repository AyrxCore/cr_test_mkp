<template>
  <RouterLink
    :to="{
      name: ProductPageList.PRODUCTS,
      query: { seller: props.seller?.externalId },
    }"
    class="flex cursor-pointer flex-col items-center rounded-lg border-2 border-solid border-secondary px-4 py-2 md:p-4"
  >
    <div class="my-1 flex h-full w-full flex-col items-center">
      <div class="seller-card">
        <img
          :alt="seller?.name"
          :src="getMediaUrl(seller?.avatar)"
          class="pointer h-[107px] w-1/3 object-contain md:w-full"
        />
        <div
          class="text-md mb-1 ml-3 w-2/3 text-start font-bold text-primary md:ml-0 md:w-auto md:text-center md:text-xl lg:text-lg"
        >
          {{ seller?.name }}
        </div>
      </div>
    </div>
  </RouterLink>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { getMediaUrl } from '@/vuejs/services/utils'
import { Seller } from '@/vuejs/types/Seller'

const props = defineProps({
  seller: {
    required: true,
    type: Object as PropType<Seller>,
  },
})
</script>

<style scoped>
@reference '@/style/main.css';

.seller-card {
  @apply mx-auto flex w-full cursor-pointer flex-row items-center justify-start rounded-lg md:mb-2 md:max-w-[200px] md:flex-col md:justify-center md:space-x-0;
}
</style>
