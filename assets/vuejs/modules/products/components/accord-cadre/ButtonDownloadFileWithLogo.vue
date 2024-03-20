<template>
  <ButtonComponent :class="classes" @click="clickOnCta(url)">
    <span class="w-full">{{ name }}</span>
  </ButtonComponent>
</template>
<script setup lang="ts">
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { openInNewTab} from '@/vuejs/services/utils'
import { isAbsoluteUrl, isFilePath } from '@/vuejs/services/urlChecker'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()
const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  url: {
    type: String,
    required: true,
  },
  classes: {
    type: String,
    required: false,
    default: '',
  },
  eventName: {
    type: String,
    required: true,
  },
  eventParams: {
    type: Object,
    required: true,
  },
})

const clickOnCta = (url: string) => {
  if (isAbsoluteUrl(url) || (!isAbsoluteUrl(url) && !isFilePath(url))) {
    openInNewTab(url)
  } else {
    productStore.downloadPdfFile(url)
  }

  sendGaEvent(props.eventName, props.eventParams)
}
</script>

<style scoped></style>
