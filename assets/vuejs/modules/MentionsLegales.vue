<template>
  <BaseTemplate title="Mentions légales | Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component :current-page="'Mentions légales'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl">
        <MentionsLegalesComponent />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import MentionsLegalesComponent from '@/vuejs/modules/shared/MentionsLegalesComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
</script>

<style scoped></style>
