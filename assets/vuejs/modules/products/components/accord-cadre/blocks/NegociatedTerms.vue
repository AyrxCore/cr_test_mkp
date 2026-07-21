<template>
  <div
    :id="negociatedTermsBlockContent.componentName"
    class="flex basis-2/3 flex-col rounded-3xl border-[5px] border-primary px-6 py-10 md:px-10 2xl:pr-40"
  >
    <!-- Titre -->
    <h2 class="text-title-primary mb-6 md:text-[28px]">
      {{ negociatedTermsBlockContent?.title }}
    </h2>

    <!-- Liste des conditions -->
    <RichTextRenderer
      :content="negociatedTermsBlockContent?.description"
      class="mb-8 max-w-none space-y-4 text-justify text-gray-600"
    />

    <!-- Section Détails et engagements (collapsible) -->
    <div
      v-if="
        negociatedTermsBlockContent?.detailsTitle &&
        negociatedTermsBlockContent?.detailsContent
      "
      class="mb-8 cursor-pointer rounded-lg bg-white px-6 py-6 shadow-md md:px-12 xs:px-8"
      @click="toggleDetails"
    >
      <button class="flex w-full items-center justify-between text-left">
        <h3 class="text-xl font-bold text-primary">
          {{ negociatedTermsBlockContent?.detailsTitle }}
        </h3>
        <AddIconComponent
          :class="{ 'rotate-45': isDetailsExpanded }"
          class="origin-center text-primary transition-transform duration-300"
        />
      </button>
      <!-- Contenu à afficher quand expanded -->
      <div
        :class="[
          isDetailsExpanded ? 'max-h-[500px] opacity-100' : 'max-h-0 opacity-0',
          'overflow-hidden transition-all duration-300 ease-in-out',
        ]"
      >
        <RichTextRenderer
          :content="negociatedTermsBlockContent?.detailsContent"
          class="mt-4 text-gray-600"
        />
      </div>
    </div>

    <!-- Boutons d'action -->
    <div class="my-auto flex h-full flex-col justify-around gap-5">
      <ButtonComponent
        v-if="negociatedTermsLayerItems.length > 0"
        class="button-primary-outline w-fit !whitespace-normal text-sm !leading-tight xs:!text-base"
        @click="layers.openNegociatedTermsLayer()"
      >
        <span class="flex items-center">
          <span class="mr-2 inline-flex items-center justify-center">
            <EyeIconComponent />
          </span>
          {{ negociatedTermsBlockContent.negociatedTermsButtonLabel }}
        </span>
      </ButtonComponent>
      <AssetButton
        v-for="(assetButton, key) in assetButtons"
        :key="key"
        :asset-button="assetButton"
        class="button-primary-outline text:sm w-fit !whitespace-normal !leading-tight xs:!text-base"
        @click="sendGtmEvent('fat_cta_generic_click', { link_text: assetButton.buttonLabel, link_url: assetButton.assetLink, origin_url: router.currentRoute.value.fullPath })"
      />
      <ButtonComponent
        v-if="shouldShowButton"
        :disabled="isNeoAutoLogin"
        class="button-primary w-fit !text-lg"
        @click="handleButtonClick"
      >
        {{ labelCtaRattachement }}
      </ButtonComponent>
      <ButtonComponent
        v-if="shouldShowContactFormButton"
        class="button-primary w-fit !text-lg"
        @click="layers.openFatInterestModal()"
      >
        Préciser mon besoin
      </ButtonComponent>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, inject, ref } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'
import { useUserStore } from '@/vuejs/stores/user.ts'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import { useAccordCadreButton } from '@/vuejs/modules/products/composables/useAccordCadreButton'

import AssetButton from '@/vuejs/modules/products/components/accord-cadre/AssetButton.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import RichTextRenderer from '@/vuejs/modules/shared/RichTextRenderer.vue'
import EyeIconComponent from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import AddIconComponent from '@/vuejs/modules/shared/icon/AddIconComponent.vue'

const accordCadreStore = useAccordCadreStore()

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { negociatedTermsBlockContent } = storeToRefs(accordCadreStore)
const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!
const isDetailsExpanded = ref(false)

const { shouldShowButton, handleButtonClick, labelCtaRattachement, shouldShowContactFormButton } =
  useAccordCadreButton(layers)


const toggleDetails = () => {
  isDetailsExpanded.value = !isDetailsExpanded.value
}

const assetButtons = computed(() => {
  return negociatedTermsBlockContent.value.assetButtons
})

const negociatedTermsLayerItems = computed(() => {
  return negociatedTermsBlockContent.value.negociatedTermsLayerItems
})
</script>
