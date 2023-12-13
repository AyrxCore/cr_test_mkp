<template>
  <BaseTemplate :title="title">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component :current-page="title" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl text-primary">
        <CmsPageComponent :page-id="pageId" class="text-primary" />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import CmsPageComponent from '@/vuejs/modules/shared/CmsPageComponent.vue'

defineProps({
  title: {
    type: String,
    required: true,
  },
  pageId: {
    type: Number,
    required: true,
  },
})
</script>

<style scoped></style>
