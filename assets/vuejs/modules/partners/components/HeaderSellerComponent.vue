<template>
  <div class="text-green mt-3.5 flex flex-col lg:flex-row lg:items-center">
    <h3 class="text-title-35 text-primary">
      {{ props.seller.name }}
    </h3>
  </div>

  <div class="mt-10 flex flex-col md:flex-row">
    <div class="flex items-center justify-center rounded-lg bg-white w-full lg:w-1/4 h-[338px] p-1 lg:mr-5">
      <img
        :src="getUpplerImage(props.seller.avatar)"
        :alt="'Logo ' + props.seller.name"
        class="items-center rounded-lg sm:mx-auto"
      />
    </div>
    <div class="col-span-3 mt-5 hidden items-center rounded-lg bg-white md:mt-0 md:flex lg:w-3/4"></div>
  </div>
</template>
<script lang="ts" setup>
import { PropType } from 'vue'
import { Seller } from '@/vuejs/types/Seller'
import { getUpplerImage } from '@/vuejs/services/utils'

const props = defineProps({
  seller: {
    required: true,
    type: Object as PropType<Seller>,
  },
})
</script>

<style scoped></style>
