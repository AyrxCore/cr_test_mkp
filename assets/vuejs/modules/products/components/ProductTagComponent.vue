<template>
  <div
    :class="compact ? 'px-2 py-1 text-xs gap-1' : 'px-3 py-1.5 text-sm gap-2'"
    class="inline-flex items-center whitespace-nowrap rounded-md border font-medium"
    :style="{ borderColor: tag.borderColor, backgroundColor: tag.bgColor, color: tag.textColor }"
  >
    <component :is="tag.icon" :class="compact ? 'h-4 w-4' : 'h-5 w-5'" class="shrink-0" />
    {{ tag.label }}
  </div>
</template>

<script lang="ts" setup>
import { ProductTagConfig } from '@/vuejs/modules/products/config/productTags'

defineProps<{
  tag: ProductTagConfig
  compact?: boolean
}>()
</script>
