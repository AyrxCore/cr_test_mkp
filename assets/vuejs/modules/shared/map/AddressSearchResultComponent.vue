<template>
  <div
    class="cursor-pointer px-4 py-2 hover:bg-slate-50"
    @click="emit('click', result)"
  >
    <div class="text-sm text-slate-900">
      {{ displayedName }}
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import { getDisplayedName } from '@/vuejs/modules/products/composables/useMap'
import type { AddressSearchResult } from '@/vuejs/services/searchAddressapi'

const emit = defineEmits<{
  (e: 'click', result: AddressSearchResult): void
}>()

const props = defineProps({
  result: {
    type: Object as () => AddressSearchResult,
    required: true,
  },
})

const displayedName = computed((): string => {
  return getDisplayedName(props.result)
})
</script>
