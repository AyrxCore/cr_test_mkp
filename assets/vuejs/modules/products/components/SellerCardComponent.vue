<template>
  <div
    class="flex cursor-pointer flex-col items-center rounded-lg border-4 border-solid border-secondary px-4 py-2 md:p-4"
    @click="goToProductResult"
  >
    <div class="my-1 flex h-full w-full flex-col items-center">
      <div class="seller-card">
        <img
          :alt="seller?.name"
          :src="getUpplerImage(seller?.avatar)"
          class="pointer h-[107px] w-1/3 object-contain md:w-full"
        />
        <div
          class="text-title-default-size mb-1 ml-3 w-2/3 text-start font-bold text-primary md:ml-0 md:w-auto md:text-center md:text-xl lg:text-lg"
        >
          {{ seller?.name }}
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { getUpplerImage } from '@/vuejs/services/utils'
import { Seller } from '@/vuejs/types/Seller'
import router from '@/vuejs/router'

const props = defineProps({
  seller: {
    required: true,
    type: Object as PropType<Seller>,
  },
})

const goToProductResult = computed(() => {
  router.push({
    name: ProductPageList.PRODUCTS,
    query: { company: props.seller?.id },
  })
})
</script>
<style scoped lang="postcss">
.seller-card {
  @apply mx-auto flex w-full cursor-pointer flex-row items-center justify-start rounded-lg md:mb-2 md:max-w-[200px] md:flex-col md:justify-center md:space-x-0;
}
</style>
