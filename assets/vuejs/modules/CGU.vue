<template>
  <BaseTemplate
    title="Conditions générales d'utilisations | Qantis - MarketPlace"
  >
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <breadcrumb-shared-component
        :current-page="'Conditions générales d\'utilisations'"
      />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl text-primary">
        <CGUComponent class="text-primary" :page="true" />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import CGUComponent from '@/vuejs/modules/shared/CGUComponent.vue'
</script>

<style scoped></style>
