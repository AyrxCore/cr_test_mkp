<template>
  <SidebarLayer
    :model-value="modelValue"
    :z-index="70"
    :show-close-button="true"
    :manage-body-scroll="false"
    @update:model-value="$emit('update:modelValue', $event)"
    @close="handleClose"
  >
    <!-- Contenu -->
    <div class="px-10 pb-6">
      <div class="flex flex-col space-y-6">
        <!-- Logo et badge -->
        <LogoBadge />

        <!-- Texte de succès -->
        <RichTextRenderer
          v-if="successText"
          :content="successText"
          class="max-w-none text-justify text-gray-600"
        />
      </div>
    </div>
  </SidebarLayer>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { storeToRefs } from 'pinia'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'

import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import RichTextRenderer from '@/vuejs/modules/shared/RichTextRenderer.vue'
import SidebarLayer from '@/vuejs/modules/shared/SidebarLayer.vue'
import LogoBadge from '@/vuejs/modules/products/components/accord-cadre/shared/LogoBadge.vue'

defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
})

defineEmits<{
  'update:modelValue': [value: boolean]
}>()

const { accordCadre } = storeToRefs(useAccordCadreStore())
const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!

const successText = computed<string>(() => {
  return accordCadre.value?.accordCadreContent?.confirmationLayerSuccess || ''
})

const handleClose = () => {
  // Ferme tous les layers (y compris celui-ci)
  // Le v-model réagit automatiquement via la réactivité de Vue
  layers.closeAllLayers()
}
</script>
