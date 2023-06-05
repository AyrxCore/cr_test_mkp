<template>
  <BaseTemplate title="Politique de confidentialité | Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component
        :current-page="'Politique de confidentialité'"
      />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <CmsPageComponent :page-id="POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID" />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import CmsPageComponent from '@/vuejs/modules/shared/CmsPageComponent.vue'
import { POLITIQUE_DE_CONFIDENTIALITE_PAGE_ID } from '@/vuejs/services/const'
</script>

<style scoped></style>
