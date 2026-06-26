<template>
  <SidebarLayer
    :model-value="modelValue"
    :z-index="60"
    show-close-button
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <!-- Contenu -->
    <div class="px-10 pb-6">
      <div class="flex flex-col space-y-6">
        <div v-for="(item, key) in negociatedTermsItems" :key="key">
          <img :src="item.imgLink" />
        </div>
      </div>
    </div>
  </SidebarLayer>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { storeToRefs } from 'pinia'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'

import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import SidebarLayer from '@/vuejs/modules/shared/SidebarLayer.vue'
import { ImageItem } from '@/vuejs/types/AccordCadre.ts'

defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
})

defineEmits<{
  'update:modelValue': [value: boolean]
}>()

const accordCadreStore = useAccordCadreStore()
const { negociatedTermsBlockContent } = storeToRefs(accordCadreStore)

const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!

const hideOverlay = computed<boolean>(
  () => layers.showNegociatedTermsLayer.value,
)

const negociatedTermsItems = computed<ImageItem[]>(() => {
  return negociatedTermsBlockContent.value?.negociatedTermsLayerItems || []
})
</script>
