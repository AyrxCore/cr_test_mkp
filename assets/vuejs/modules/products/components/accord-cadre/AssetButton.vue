<template>
  <a
    v-if="isMail"
    v-bind="$attrs"
    :href="mailtoHref"
    class="button"
  >
    <span class="flex items-center">
      <span class="mr-2 inline-flex items-center justify-center">
        <MailIconComponent fill="currentColor" width="24" />
      </span>
      {{ assetButton.buttonLabel }}
    </span>
  </a>
  <ButtonComponent
    v-else
    v-bind="$attrs"
    @click="handleAssetButtonClick(assetButton)"
  >
    <span class="flex items-center">
      <span class="mr-2 inline-flex items-center justify-center">
        <component
          :is="getIconComponent(assetType)"
          fill="currentColor"
          width="24"
        />
      </span>
      {{ assetButton.buttonLabel }}
    </span>
  </ButtonComponent>
</template>

<script lang="ts" setup>
import { PropType, computed, ref } from 'vue'

import { useProductStore } from '@/vuejs/stores/product'
import { isAbsoluteUrl, isFilePath } from '@/vuejs/services/urlChecker'
import { openInNewTab } from '@/vuejs/services/utils'
import { AssetButton } from '@/vuejs/types/AccordCadre'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import PdfIconComponent from '@/vuejs/modules/shared/icon/PdfIconComponent.vue'
import ExcelIconComponent from '@/vuejs/modules/shared/icon/ExcelIconComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import RedirectionIconComponent from '@/vuejs/modules/shared/icon/RedirectionIconComponent.vue'

defineOptions({ inheritAttrs: false })

const props = defineProps({
  assetButton: {
    required: true,
    type: Object as PropType<AssetButton>,
  },
})

const isLoading = ref<boolean>(false)

const productStore = useProductStore()

const AssetLinkType = {
  PDF: 'pdf',
  EXCEL: 'excel',
  MAIL: 'mail',
  URL: 'url',
} as const

type AssetLinkTypeValue = (typeof AssetLinkType)[keyof typeof AssetLinkType]

const getAssetLinkType = (link: string): AssetLinkTypeValue => {
  const lowerLink = link.toLowerCase()

  if (lowerLink.endsWith('.pdf')) {
    return AssetLinkType.PDF
  }

  if (/\.(xls|xlsx|xlsm|csv)$/.test(lowerLink)) {
    return AssetLinkType.EXCEL
  }

  if (lowerLink.includes('@')) {
    return AssetLinkType.MAIL
  }

  return AssetLinkType.URL
}

const assetType = computed(() => getAssetLinkType(props.assetButton.assetLink))

const isMail = computed(() => assetType.value === AssetLinkType.MAIL)

const mailtoHref = computed(() => {
  const link = props.assetButton.assetLink
  return link.startsWith('mailto:') ? link : `mailto:${link}`
})

const getIconComponent = (type: AssetLinkTypeValue) => {
  switch (type) {
    case AssetLinkType.PDF:
      return PdfIconComponent
    case AssetLinkType.EXCEL:
      return ExcelIconComponent
    case AssetLinkType.URL:
    default:
      return RedirectionIconComponent
  }
}

const clickOnCta = async (url: string) => {
  if (isAbsoluteUrl(url) || (!isAbsoluteUrl(url) && !isFilePath(url))) {
    openInNewTab(url)
  } else {
    isLoading.value = true
    await productStore.downloadPdfFile(url)
    isLoading.value = false
  }
}

const handleAssetButtonClick = (assetButton: AssetButton) => {
  if (assetType.value === AssetLinkType.PDF) {
    clickOnCta(assetButton.assetLink)
  } else if (assetType.value === AssetLinkType.EXCEL) {
    // Téléchargement
    const anchor = document.createElement('a')
    anchor.href = assetButton.assetLink
    anchor.download = assetButton.buttonLabel || 'document'
    anchor.target = '_blank'
    anchor.rel = 'noopener noreferrer'
    document.body.appendChild(anchor)
    anchor.click()
    document.body.removeChild(anchor)
  } else {
    // Redirection
    const urlToOpen = assetButton.assetLink.match(/^https?:\/\//)
      ? assetButton.assetLink
      : `https://${assetButton.assetLink}`
    window.open(urlToOpen, '_blank')
  }
}
</script>
