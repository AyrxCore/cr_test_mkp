<template>
  <div class="text-center text-lg leading-5 text-white">
    <div
      v-if="currentStatus.status === status.pending"
      class="mx-auto flex justify-center border p-2 lg:w-2/3"
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
    >
      Votre rattachement est en cours
      <PendingIconComponent
        class="ml-1 w-5"
        :fill="betterTextColor('primary')"
        :stroke="betterTextColor('primary')"
      />
    </div>
    <div
      v-else
      class="mx-auto flex justify-center border p-2 lg:w-2/3"
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
    >
      <CheckIconComponent :stroke="betterTextColor('primary')" class="mr-4" />
      Vous bénéficiez des conditions
    </div>
    <div class="condition-beneficiaire mt-4">
      <p v-html="text" />
    </div>

    <div class="mt-6 flex flex-col items-center">
      <ButtonDownloadFileWithLogo
        v-if="cta1.name && cta1.url"
        event-name="click_fat_cta_1"
        :event-params="{
          product_name: props.accordName,
          state_rattachement: props.currentStatus.status,
        }"
        classes="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        :url="formatUrlWithChannelCode(cta1.url)"
        :name="cta1.name"
      />
      <a
        v-else-if="cta1.name && cta1.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        :href="cta1.mailto"
        @click="gtmEvent(1)"
      >
        <span>
          {{ cta1.name }}
        </span>
      </a>
      <ButtonComponent
        v-if="cta2.name && cta2.url"
        class="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="clickOnCta(cta2.url, 2)"
      >
        {{ cta2.name }}
      </ButtonComponent>
      <a
        v-else-if="cta2.name && cta2.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        :href="cta2.mailto"
        @click="gtmEvent(2)"
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
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import PendingIconComponent from '@/vuejs/modules/shared/icon/PendingIconComponent.vue'
import ButtonDownloadFileWithLogo from '@/vuejs/modules/products/components/accord-cadre/ButtonDownloadFileWithLogo.vue'
import { status } from '@/vuejs/modules/products'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { betterTextColor, openInNewTab } from '@/vuejs/services/utils'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { useChannelStore } from '@/vuejs/stores/channel'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'

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

const clickOnCta = (buttonUrl: string, ctaNumber: number) => {
  openInNewTab(formatUrlWithChannelCode(buttonUrl))
  gtmEvent(ctaNumber)
}

const gtmEvent = (ctaNumber) => {
  const eventName = ctaNumber === 1 ? 'click_fat_cta_1' : 'click_fat_cta_2'
  sendGaEvent(eventName, {
    product_name: props.accordName,
    state_rattachement: props.currentStatus.status,
  })
}
</script>

<style scoped></style>
