<template>
  <SidebarLayer
    :model-value="modelValue"
    :z-index="60"
    :hide-overlay="hideOverlay"
    :manage-body-scroll="false"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <!-- Contenu -->
    <div class="px-10 pb-6">
      <div class="flex flex-col space-y-6">
        <!-- Logo et badge -->
        <LogoBadge />

        <!-- Texte de confirmation -->
        <RichTextRenderer
          v-if="confirmationText"
          :content="confirmationText"
          class="max-w-none text-justify text-gray-600"
        />

        <!-- Bouton Confirmer -->
        <div class="pt-4">
          <ButtonComponent
            class="button-primary w-fit"
            :is-loading="isLoading"
            @click="handleConfirm"
          >
            Confirmer
          </ButtonComponent>
        </div>
      </div>
    </div>
  </SidebarLayer>
</template>

<script lang="ts" setup>
import { computed, inject, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'
import { useChannelStore } from '@/vuejs/stores/channel'

import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
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

const accordCadreStore = useAccordCadreStore()
const { accordCadre } = storeToRefs(accordCadreStore)
const { formattedPhoneNumber } = storeToRefs(useChannelStore())
const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!

const isLoading = ref<boolean>(false)

const confirmationText = computed<string>(() => {
  return (
    accordCadre.value?.accordCadreContent?.confirmationLayerDescription ||
    formattedPhoneNumber.value ||
    ''
  )
})

const hideOverlay = computed<boolean>(() => layers.showSuccessLayer.value)

const handleConfirm = async () => {
  isLoading.value = true
  try {
    const success = await accordCadreStore.attachAccordCadre()
    if (success) {
      layers.openSuccessLayer()
    }
  } finally {
    isLoading.value = false
  }
}
</script>
