<template>
  <h3 class="mb-2 text-lg text-primary sm:text-[25px]">
    <slot name="title" />
  </h3>
  <div
    class="mb-8 flex items-center justify-between rounded-lg bg-white py-3 px-3 text-base text-gray-500 md:text-lg"
  >
    <div class="truncate">
      <span v-if="props.address" :title="props.address">
        {{ props.address }}
      </span>
      <span v-else>
        <LoaderSharedComponent class="text-secondary" />
      </span>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { PropType } from 'vue'

import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { Address } from '@/vuejs/types/Address'

const props = defineProps({
  address: {
    required: false,
    type: Object as PropType<Address>,
  },
})
</script>
