<template>
  <h3 class="mb-2 text-lg text-primary sm:text-[25px]">
    <slot name="title" />
  </h3>
  <div
    class="mb-8 flex items-center justify-between rounded-lg bg-white py-3 px-3 text-base text-gray-500 md:text-lg"
  >
    <div>
      <span v-if="address">
        {{ !address.company ? '' : address.company + ', ' }}
        {{ address.street }}
        {{ address.postcode }}
        {{ address.city }}
      </span>
      <span v-else>
        <LoaderSharedComponent class="text-purple-600" />
      </span>
    </div>
  </div>
</template>
<script lang="ts" setup>
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { PropType } from 'vue'
import { Address } from '@/vuejs/types/Address'

const props = defineProps({
  address: {
    required: false,
    type: Object as PropType<Address>,
  },
})
</script>
