<template>
  <div class="container mx-auto my-10 mt-[5%] px-5 text-primary lg:px-0">
    <div
      v-if="content"
      v-html="content"
      class="px-3 [&_ol]:mb-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:mb-3 [&_ul]:list-disc [&_ul]:pl-5"
    />
    <LoadingComponent v-else />
  </div>
</template>

<script lang="ts" setup>
import { onMounted, computed } from 'vue'

import { useLegalContentStore } from '../../stores/legalContent'

import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const legalContentStore = useLegalContentStore()

const props = defineProps<{
  field: 'cgu' | 'legalTerms' | 'privacyPolicy'
}>()

onMounted(async (): Promise<void> => {
  if (!legalContentStore.legalContent) {
    await legalContentStore.fetch()
  }
})

const content = computed((): string | null | undefined => {
  return legalContentStore.legalContent?.[props.field]
})
</script>

<style scoped>
.mentions-legales p {
  @apply text-justify !text-sm md:!text-base lg:!text-lg;
}
</style>
