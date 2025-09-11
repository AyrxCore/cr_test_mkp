<template>
  <div class="text-center text-lg leading-5 text-white">
    <div class="condition-beneficiaire mt-4">
      <p v-html="properties.process_fat_client" />
    </div>

    <div class="mt-6 flex flex-col items-center">
      <ButtonComponent
        v-if="cta1.name && cta1.url"
        :disabled="isNeoAutoLogin"
        class="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="openingNewTab(cta1)"
      >
        <span>
          {{ cta1.name }}
        </span>
      </ButtonComponent>
      <a
        v-else-if="cta1.name && cta1.mailto"
        :class="{
          disabled: isNeoAutoLogin,
        }"
        :href="cta1.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="
          sendGtmEvent('fat_cta_generic_click', {
            type: 'email',
            link_text: cta1.name,
            link_url: cta1.mailto,
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      >
        <span>
          {{ cta1.name }}
        </span>
      </a>
      <ButtonComponent
        v-if="cta2.name && cta2.url"
        :disabled="isNeoAutoLogin"
        class="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="openingNewTab(cta2)"
      >
        {{ cta2.name }}
      </ButtonComponent>
      <a
        v-else-if="cta2.name && cta2.mailto"
        :class="{
          disabled: isNeoAutoLogin,
        }"
        :href="cta2.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="
          sendGtmEvent('fat_cta_generic_click', {
            type: 'email',
            link_text: cta2.name,
            link_url: cta2.mailto,
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      >
        <span>
          {{ cta2.name }}
        </span>
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { useUserStore } from '@/vuejs/stores/user'
import { openInNewTab } from '@/vuejs/services/utils'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

const { isNeoAutoLogin } = storeToRefs(useUserStore())

const props = defineProps({
  properties: {
    type: Object,
    default: null,
  },
  accordName: {
    type: String,
    default: null,
  },
})

interface CTA {
  name: string | null
  url: string | null
  mailto: string | null
}

const cta1 = computed<CTA>(() => {
  return {
    name: props.properties.cta1_txt_fat_client,
    url: props.properties.cta1_link_fat_client,
    mailto: props.properties.cta1_mailto_fat_client,
  }
})

const cta2 = computed<CTA>(() => {
  return {
    name: props.properties.cta2_txt_fat_client,
    url: props.properties.cta2_link_fat_client,
    mailto: props.properties.cta2_mailto_fat_client,
  }
})

const openingNewTab = (cta: CTA) => {
  openInNewTab(formatUrlWithChannelCode(cta.url))
  sendGtmEvent('fat_cta_generic_click', {
    type: 'link',
    link_text: cta.name,
    link_url: formatUrlWithChannelCode(cta.url),
    origin_url: router.currentRoute.value.fullPath,
  })
}
</script>
