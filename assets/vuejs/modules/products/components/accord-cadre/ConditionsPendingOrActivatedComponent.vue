<template>
  <div class="text-center text-lg leading-5 text-white">
    <!-- Badge statut PENDING -->
    <div
      v-if="currentStatus.status === status.pending"
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
      class="mx-auto flex w-fit justify-center border px-6 py-2"
    >
      <PendingIconComponent
        :fill="betterTextColor('primary')"
        :stroke="betterTextColor('primary')"
        class="mr-4"
      />
      {{
        properties.label_process_pending || 'Votre rattachement est en cours'
      }}
    </div>

    <ButtonComponent
      v-else-if="accord?.formWithMessageFat && currentStatus.status === status.activated"
      :class="{
        disabled: isNeoAutoLogin,
      }"
      class="button-primary mx-auto whitespace-normal border-2 border-solid !border-white"
      @click="openInterestModal"
    >
      <ArrowRightIconComponent class="mr-2 h-4 w-4" />
      {{ properties.label_cta_form_with_message || 'Préciser mon besoin véhicule' }}
    </ButtonComponent>

    <!-- Badge statut ACTIVATED standard (sans formWithMessageFat) -->
    <div
      v-else
      :class="
        'border-' +
        betterTextColor('primary') +
        ' text-' +
        betterTextColor('primary')
      "
      class="mx-auto flex w-fit justify-center border px-6 py-2"
    >
      <CheckIconComponent :stroke="betterTextColor('primary')" class="mr-4" />
      {{
        properties.label_process_activated || 'Vous bénéficiez des conditions'
      }}
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

    <FatInterestModal
      v-if="showInterestModal"
      v-model="showInterestModal"
      :accord="accord"
    />
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
import { betterTextColor } from '@/vuejs/services/utils'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { Product } from '@/vuejs/types/Product'
import { status } from '@/vuejs/modules/products'

import ButtonDownloadFileWithLogo from '@/vuejs/modules/products/components/accord-cadre/ButtonDownloadFileWithLogo.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import PendingIconComponent from '@/vuejs/modules/shared/icon/PendingIconComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import FatInterestModal from '@/vuejs/modules/products/components/accord-cadre/FatInterestModal.vue'

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
  accord: {
    type: Object as PropType<Product>,
    default: null,
  },
})

const channelStore = useChannelStore()

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const currentChannel = channelStore.currentChannel

const showInterestModal = ref<boolean>(false)

const openInterestModal = () => {
  showInterestModal.value = true
  sendGtmEvent('fat_cta_preciser_besoin_click', {
    link_text: 'Préciser mon besoin véhicule',
    origin_url: router.currentRoute.value.fullPath,
    accord_name: props.accordName,
  })
}

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
