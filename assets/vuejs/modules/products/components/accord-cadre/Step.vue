<template>
  <div class="text-center">
    <div
      class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-secondary text-lg font-bold text-white"
    >
      0{{ stepKey }}
    </div>
    <h3 class="mb-3 text-[23px] font-bold">{{ stepItem.title }}</h3>
    <p class="text-md text-gray-200">
      {{ stepItem.description }}
    </p>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'

import { StepItem } from '@/vuejs/types/AccordCadre.ts'

const props = defineProps({
  stepItem: {
    required: true,
    type: Object as PropType<StepItem>,
  },
  stepKey: {
    type: Number,
    required: true,
  },
})
</script>
