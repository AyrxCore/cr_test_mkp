<template>
  <SidebarLayer
    :hide-overlay="hideOverlay"
    :model-value="modelValue"
    show-close-button
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <!-- Contenu -->
    <div class="px-10 pb-6 text-justify">
      <div class="flex flex-col space-y-6">
        <!-- Logo et badge -->
        <LogoBadge />

        <!-- Description -->
        <RichTextRenderer
          v-if="presentationBlockContent.layerMoreInformationsDescription"
          :content="presentationBlockContent.layerMoreInformationsDescription"
          class="max-w-none text-gray-600"
        />

        <!-- Bouton Activer mes avantages -->
        <div v-if="shouldShowButton" class="py-4">
          <a
            v-if="isActivatedWithUrl"
            :href="urlCtaRattachement"
            rel="noopener noreferrer"
            target="_blank"
          >
            <ButtonComponent
              :disabled="isNeoAutoLogin"
              class="button-primary w-fit !text-base"
            >
              {{ labelCtaRattachement }}
            </ButtonComponent>
          </a>
          <ButtonComponent
            v-else
            :disabled="isNeoAutoLogin"
            class="button-primary w-fit !text-base"
            @click="handleButtonClick"
          >
            {{ labelCtaRattachement }}
          </ButtonComponent>
        </div>

        <!-- Bouton Préciser mon besoin -->
        <div v-if="shouldShowContactFormButton" class="py-4">
          <ButtonComponent
            class="button-primary w-fit !text-base"
            @click="layers.openFatInterestModal()"
          >
            Préciser mon besoin
          </ButtonComponent>
        </div>

        <!-- Téléphone -->
        <div v-if="phoneNumber" class="py-8">
          <a
            :href="`tel:${phoneNumber}`"
            class="font-cotext text-2xl font-bold text-primary hover:text-primary"
          >
            {{ phoneNumber }}
          </a>
          <RichTextRenderer
            v-if="
              presentationBlockContent.layerMoreInformationsPhoneDescription
            "
            :content="
              presentationBlockContent.layerMoreInformationsPhoneDescription
            "
            class="max-w-none text-sm text-gray-600"
          />
        </div>

        <!-- Section Documents à télécharger -->
        <template v-if="assetButtons.length > 0">
          <h3 class="text-2xl font-bold text-primary">
            Documents <br class="xs:hidden" />
            à télécharger
          </h3>

          <!-- Boutons d'assets -->
          <div class="flex flex-col gap-4">
            <AssetButtonComponent
              v-for="(assetButton, key) in assetButtons"
              :key="key"
              :asset-button="assetButton"
              class="button-primary-outline w-fit !whitespace-normal !text-base"
            />
          </div>
        </template>
      </div>
    </div>
  </SidebarLayer>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { storeToRefs } from 'pinia'

import { AssetButton } from '@/vuejs/types/AccordCadre'
import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'
import { useUserStore } from '@/vuejs/stores/user.ts'

import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import { useAccordCadreButton } from '@/vuejs/modules/products/composables/useAccordCadreButton'
import AssetButtonComponent from '@/vuejs/modules/products/components/accord-cadre/AssetButton.vue'
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
const { presentationBlockContent } = storeToRefs(accordCadreStore)
const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!

const { isNeoAutoLogin } = storeToRefs(useUserStore())

const {
  urlCtaRattachement,
  shouldShowButton,
  isActivatedWithUrl,
  handleButtonClick,
  labelCtaRattachement,
  shouldShowContactFormButton,
} = useAccordCadreButton(layers)


const assetButtons = computed<AssetButton[]>(() => {
  return presentationBlockContent.value?.layerMoreInformationsAssetButtons || []
})

const phoneNumber = computed<string>(() => {
  return presentationBlockContent.value?.layerMoreInformationsPhone || ''
})

const hideOverlay = computed<boolean>(
  () => layers.showConfirmationLayer.value || layers.showSuccessLayer.value,
)
</script>
