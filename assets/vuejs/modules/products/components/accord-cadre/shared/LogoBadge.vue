<template>
  <div class="flex flex-col items-start gap-3">
    <img
      :src="bannerBlockContent.logoUrl"
      alt="Partner Logo"
      class="h-24 w-auto object-contain"
    />
    <div class="font-cotext text-2xl font-bold leading-tight text-primary">
      {{ badgeText }}
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'

const { bannerBlockContent } = storeToRefs(useAccordCadreStore())

const badgeText = computed<string>(() => {
  return `${bannerBlockContent.value.badgeTextTop ?? ''} ${bannerBlockContent.value.badgeTextBottom ?? ''}`.trim()
})
</script>
