<template>
  <a
    class="default-button-gradient mb-2 inline-flex items-center rounded-full px-5 py-2.5 text-sm font-medium text-white hover:from-purple-500 hover:to-purple-500"
    :href="href"
    :target="target"
  >
    <slot name="default" />
  </a>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'

const props = defineProps({
  href: {
    required: true,
    type: String as PropType<HTMLAnchorElement['href']>,
  },
  target: {
    required: false,
    type: String as PropType<HTMLAnchorElement['target']>,
    default: '',
  },
})
</script>
