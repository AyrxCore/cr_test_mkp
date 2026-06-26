<template>
  <div class="py-6">
    <!-- Breadcrumb -->
    <BreadcrumbSharedComponent
      :current-page="accordCadre.name"
      :list-url="breadcrumbUrl"
      class="ml-6 lg:mb-6"
      gtm-event-name="click_product_breadcrumb"
    />

    <!-- Top Banner Section -->
    <div class="relative h-64 w-full">
      <!-- Background Image -->
      <img
        :src="bannerBlockContent?.imgBannerUrlDesktop"
        alt="Banner"
        class="hidden h-full w-full rounded-3xl object-cover sm:block"
      />
      <img
        :src="bannerBlockContent?.imgBannerUrlMobile"
        alt="Banner"
        class="block h-full w-full rounded-3xl object-cover sm:hidden"
      />

      <!-- Overlays Container -->
      <div
        class="absolute -bottom-24 mb-6 ml-6 flex flex-col items-start gap-6 sm:bottom-0 sm:flex-row sm:items-stretch"
      >
        <!-- Logo Box -->
        <div
          class="flex h-[100px] w-fit items-center justify-center rounded-xl bg-white p-1 shadow-sm"
        >
          <img
            :src="bannerBlockContent?.logoUrl"
            alt="Partner Logo"
            class="h-full w-auto object-contain"
          />
        </div>

        <!-- Discount Badge -->
        <div
          class="md:max-w-[400px] sm:max-w-[350px] flex h-[100px] max-w-[300px] flex-col justify-center rounded-xl bg-secondary px-5 py-2 font-cotext text-white shadow-sm"
        >
          <span class="leading-tight">{{
            bannerBlockContent?.badgeTextTop
          }}</span>
          <span
            class="break-words text-[24px] font-bold leading-none sm:text-[32px]"
            >{{ bannerBlockContent?.badgeTextBottom }}</span
          >
        </div>
      </div>
    </div>

    <!-- Content Header -->
    <div class="mt-16 flex flex-col justify-between pt-6 sm:mt-0">
      <h1 class="text-title-primary mb-4 pl-6 md:text-[28px]">
        {{ accordCadre?.accordCadreContent?.name }}
      </h1>
      <div class="flex flex-col justify-between sm:flex-row">
        <div class="mb-4 flex items-center gap-2 pl-6 sm:mb-0">
          <span :class="statusColorClass" class="h-3 w-3 rounded-full"></span>
          <span class="font-cotext text-lg font-bold text-gray-700">{{
            labelStatus?.label
          }}</span>
        </div>
        <!-- CTA Button -->
        <div class="flex flex-col gap-2 sm:flex-row">
          <ButtonComponent
            v-if="shouldShowButton"
            :disabled="isNeoAutoLogin"
            class="button-primary !text-lg"
            @click="handleButtonClick"
          >
            {{ labelCtaRattachement }}
          </ButtonComponent>
          <ButtonComponent
            v-if="shouldShowContactFormButton"
            class="button-primary !text-lg"
            @click="layers.openFatInterestModal()"
          >
            Préciser mon besoin
          </ButtonComponent>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, inject } from 'vue'
import { storeToRefs } from 'pinia'

import { PageList } from '@/vuejs/router.ts'
import { useUserStore } from '@/vuejs/stores/user.ts'
import { BreadcrumbItem } from '@/vuejs/types/Breadcrumb.ts'
import { AccountAccordCadreStatus } from '@/vuejs/types/AccountAccordCadre'
import type { AccordCadreLayersComposable } from '@/vuejs/modules/products/composables/useAccordCadreLayers'
import { useAccordCadreButton } from '@/vuejs/modules/products/composables/useAccordCadreButton'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import { useAccordCadreStore } from '@/vuejs/stores/accordCadre.ts'

const { bannerBlockContent, labelStatus, accordCadre } = storeToRefs(
  useAccordCadreStore(),
)
const layers = inject<AccordCadreLayersComposable>('accordCadreLayers')!

const { isNeoAutoLogin } = storeToRefs(useUserStore())

const {
  shouldShowButton,
  handleButtonClick,
  labelCtaRattachement,
  shouldShowContactFormButton,
} = useAccordCadreButton(layers)

const breadcrumbUrl = computed<BreadcrumbItem[]>(() => {
  const breadcrumb = []
  if (accordCadre.value?.categories?.length > 0) {
    for (const category of accordCadre.value.categories) {
      breadcrumb.push({
        id: category.id,
        name: category.name,
        url: {
          name: PageList.ACCORD_CADRE,
          query: { category: category.id },
        },
      })
    }
  }

  return breadcrumb
})

const statusColorClass = computed<string>(() => {
  switch (labelStatus.value.status) {
    case AccountAccordCadreStatus.NOT_ACTIVATED:
      return 'bg-red-500'
    case AccountAccordCadreStatus.ACTIVATED:
      return 'bg-green-500'
    case AccountAccordCadreStatus.PENDING:
      return 'bg-orange-500'
    default:
      return 'bg-red-500'
  }
})
</script>
