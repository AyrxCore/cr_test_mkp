<template>
  <div class="text-center text-lg leading-5 text-white">
    <div
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
      class="mx-auto flex w-fit justify-center border px-6 py-2"
    >
      <template v-if="currentStatus.status === status.pending">
        <PendingIconComponent
          :fill="betterTextColor('primary')"
          :stroke="betterTextColor('primary')"
          class="mr-4"
        />
        {{
          properties.label_process_pending || 'Votre rattachement est en cours'
        }}
      </template>
      <template v-else>
        <CheckIconComponent :stroke="betterTextColor('primary')" class="mr-4" />
        {{
          properties.label_process_activated || 'Vous bénéficiez des conditions'
        }}
      </template>
    </div>

    <div class="condition-beneficiaire mt-4">
      <p v-html="text" />
    </div>

    <div class="mt-6 flex flex-col items-center">
      <ButtonDownloadFileWithLogo
        v-if="cta1.name && cta1.url"
        :disabled="isNeoAutoLogin"
        :name="cta1.name"
        :url="formatUrlWithChannelCode(cta1.url)"
        classes="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="
          sendGtmEvent('fat_cta_generic_click', {
            type: 'link',
            link_text: cta1.name,
            link_url: formatUrlWithChannelCode(cta1.url),
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      />
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
      <ButtonDownloadFileWithLogo
        v-if="cta2.name && cta2.url"
        :disabled="isNeoAutoLogin"
        :name="cta2.name"
        :url="formatUrlWithChannelCode(cta2.url)"
        classes="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="
          sendGtmEvent('fat_cta_generic_click', {
            type: 'link',
            link_text: cta2.name,
            link_url: cta2.url,
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      />
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
import { computed, PropType } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
import { betterTextColor } from '@/vuejs/services/utils'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { status } from '@/vuejs/modules/products'

import ButtonDownloadFileWithLogo from '@/vuejs/modules/products/components/accord-cadre/ButtonDownloadFileWithLogo.vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import PendingIconComponent from '@/vuejs/modules/shared/icon/PendingIconComponent.vue'

const props = defineProps({
  currentStatus: {
    type: Object as PropType<AccountAccordCadre>,
    required: true,
  },
  properties: {
    type: Object,
    default: null,
  },
  accordName: {
    type: String,
    default: null,
  },
})

const channelStore = useChannelStore()

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const currentChannel = channelStore.currentChannel

const text = computed(() => {
  if (currentChannel.code === 'QANTIS_ACHAT') {
    return props.currentStatus.status === status.value.pending
      ? props.properties.process_pending
      : props.properties.process_activated
  } else {
    return props.currentStatus.status === status.value.pending
      ? props.properties.process_pending_mb
      : props.properties.process_activated_mb
  }
})

const cta1 = computed(() => {
  if (props.currentStatus.status === status.value.pending) {
    return {
      name: props.properties.cta1_text_pending,
      url: props.properties.cta1_link_pending,
      mailto: props.properties.cta1_mailto_pending,
    }
  } else {
    return {
      name: props.properties.cta1_text_activated,
      url: props.properties.cta1_link_activated,
      mailto: props.properties.cta1_mailto_activated,
    }
  }
})

const cta2 = computed(() => {
  if (props.currentStatus.status === status.value.pending) {
    return {
      name: props.properties.cta2_text_pending,
      url: props.properties.cta2_link_pending,
      mailto: props.properties.cta2_mailto_pending,
    }
  } else {
    return {
      name: props.properties.cta2_text_activated,
      url: props.properties.cta2_link_activated,
      mailto: props.properties.cta2_mailto_activated,
    }
  }
})
</script>
