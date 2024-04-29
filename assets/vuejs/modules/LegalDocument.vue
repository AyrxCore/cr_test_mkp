<template>
  <BaseTemplate :title="title">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8">
      <BreadcrumbSharedComponent :current-page="title" />
      <div class="m-auto my-2 w-[100%] max-w-screen-2xl text-primary">
        <CmsPageComponent :page-id="pageId" class="text-primary" />
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { PageList } from '@/vuejs/router'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import CmsPageComponent from '@/vuejs/modules/shared/CmsPageComponent.vue'

import { useChannelStore } from '@/vuejs/stores/channel'

const channelStore = useChannelStore()

defineProps({
  title: {
    type: String,
    required: true,
  },
  page: {
    type: String,
    required: true,
  },
})

const channelDocuments = channelStore.currentChannel.documents

const pageId = computed(() => {
  switch (props.page) {
    case PageList.MENTIONS_LEGALES_PAGE:
      return channelDocuments.legalTerms
    case PageList.CGU_PAGE:
      return channelDocuments.generalTermsOfUse
    case PageList.POLITIQUE_DE_CONFIDENTIALITE:
      return channelDocuments.privacyPolicy
    default:
      break
  }
})
</script>

<style scoped></style>
