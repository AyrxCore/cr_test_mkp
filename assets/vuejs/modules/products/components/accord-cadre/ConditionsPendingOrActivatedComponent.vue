<template>
  <div class="text-center text-lg leading-5 text-white">
    <div
      v-if="currentStatus.status === status.pending"
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
      class="mx-auto flex justify-center border p-2 lg:w-2/3"
    >
      Votre rattachement est en cours
      <PendingIconComponent
        :fill="betterTextColor('primary')"
        :stroke="betterTextColor('primary')"
        class="ml-1 w-5"
      />
    </div>
    <div
      v-else
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
      class="mx-auto flex justify-center border p-2 lg:w-2/3"
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
        :disabled="isNeoAutoLogin"
        :event-params="{
          product_name: props.accordName,
          state_rattachement: props.currentStatus.status,
        }"
        :name="cta1.name"
        :url="formatUrlWithChannelCode(cta1.url)"
        classes="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        event-name="click_fat_cta_1"
      />
      <a
        v-else-if="cta1.name && cta1.mailto"
        :class="{
          disabled: isNeoAutoLogin,
        }"
        :href="cta1.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="gtmEvent(1)"
      >
        <span>
          {{ cta1.name }}
        </span>
      </a>
      <ButtonDownloadFileWithLogo
        v-if="cta2.name && cta2.url"
        :disabled="isNeoAutoLogin"
        :event-params="{
          product_name: props.accordName,
          state_rattachement: props.currentStatus.status,
        }"
        :name="cta2.name"
        :url="formatUrlWithChannelCode(cta2.url)"
        classes="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        event-name="click_fat_cta_2"
      />
      <a
        v-else-if="cta2.name && cta2.mailto"
        :class="{
          disabled: isNeoAutoLogin,
        }"
        :href="cta2.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
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
import { storeToRefs } from 'pinia'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import PendingIconComponent from '@/vuejs/modules/shared/icon/PendingIconComponent.vue'
import ButtonDownloadFileWithLogo from '@/vuejs/modules/products/components/accord-cadre/ButtonDownloadFileWithLogo.vue'
import { status } from '@/vuejs/modules/products'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { betterTextColor } from '@/vuejs/services/utils'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'

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

const gtmEvent = (ctaNumber: number) => {
  const eventName = ctaNumber === 1 ? 'click_fat_cta_1' : 'click_fat_cta_2'
  sendGaEvent(eventName, {
    product_name: props.accordName,
    state_rattachement: props.currentStatus.status,
  })
}
</script>

<style scoped></style>
