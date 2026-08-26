<template>
  <div class="basis-1/3 pb-6 pt-0 text-justify xl:pr-8">
    <!-- Note RSE -->
    <div
      v-if="presentationBlockContent?.rseScore"
      class="mb-4 font-cotext"
      @click="scrollToRseBlock"
    >
      <p class="text-md mt-2 font-medium text-green-600">Note RSE</p>
      <div class="flex items-center gap-3">
        <LeafIconComponent class="text-green-600" />
        <p class="text-xl font-bold text-green-600">
          {{ presentationBlockContent.rseScore }}/10
        </p>
      </div>
    </div>

    <!-- Titre -->
    <h2
      :id="presentationBlockContent?.componentName"
      class="mb-4 font-cotext text-lg font-bold text-gray-700 md:text-xl"
    >
      {{ presentationBlockContent?.title }}
    </h2>

    <!-- Points clés (bulletpoints) -->
    <RichTextRenderer
      v-if="presentationBlockContent?.bulletpoints"
      :content="presentationBlockContent.bulletpoints"
      class="mb-4 max-w-none space-y-2 font-cotext text-gray-600"
      with-checkmarks
    />

    <!-- Description -->
    <RichTextRenderer
      :content="presentationBlockContent?.description"
      class="mb-6 max-w-none space-y-4 text-gray-600"
    />

    <!-- Bouton En savoir plus -->
    <div
      v-if="
        presentationBlockContent?.layerMoreInformationsDescription ||
        presentationBlockContent?.layerMoreInformationsPhone ||
        assetButtons.length > 0
      "
      class="space-y-4"
    >
      <ButtonComponent
        class="button-primary-outline w-fit text-base!"
        @click="layers.openMoreInformationsLayer()"
      >
        En savoir plus
      </ButtonComponent>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { storeToRefs } from 'pinia'

import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'
import { AssetButton } from '@/vuejs/types/AccordCadre'

import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import RichTextRenderer from '@/vuejs/modules/shared/RichTextRenderer.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'

const { presentationBlockContent } = storeToRefs(useAccordCadreStore())
const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!

const assetButtons = computed<AssetButton[]>(() => {
  return (
    presentationBlockContent?.value?.layerMoreInformationsAssetButtons || []
  )
})

const scrollToRseBlock = () => {
  // useScrollToElement('rseBlock') // TODO: Récupérer le nom du bloc quand on l'implémentera
}
</script>
