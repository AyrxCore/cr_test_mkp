<template>
  <ButtonComponent
    :class="classes"
    :disabled="disabled"
    :is-loading="isLoading"
    @click="clickOnCta(url)"
  >
    <span class="w-full">{{ name }}</span>
  </ButtonComponent>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import { useProductStore } from '@/vuejs/stores/product'
import { isAbsoluteUrl, isFilePath } from '@/vuejs/services/urlChecker'
import { openInNewTab } from '@/vuejs/services/utils'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

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
  disabled: {
    type: Boolean,
    required: false,
    default: false,
  },
})

const productStore = useProductStore()

const isLoading = ref<boolean>(false)

const clickOnCta = async (url: string) => {
  if (isAbsoluteUrl(url) || (!isAbsoluteUrl(url) && !isFilePath(url))) {
    openInNewTab(url)
  } else {
    isLoading.value = true
    await productStore.downloadPdfFile(url)
    isLoading.value = false
  }
}
</script>
