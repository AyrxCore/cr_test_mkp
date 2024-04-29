<template>
  <div class="flex h-3/5 w-full justify-center">
    <img
      class=""
      :src="channel?.options?.PRE_HOME_IMAGE"
      :alt="`Logo ${channel?.name}`"
    />
  </div>
  <div class="mt-3 flex h-full flex-col justify-between">
    <div>
      <h3 class="mb-6 text-xl font-bold text-gray-900 lg:text-3xl">
        {{ channel?.options?.PRE_HOME_TITLE }}
      </h3>
      <p class="text-sm font-bold text-gray-700 lg:text-base">
        {{ channel?.options?.PRE_HOME_SUBTITLE }}
      </p>
      <ul class="ml-6 list-disc text-xs text-gray-700 lg:text-sm">
        <li v-for="(item, key) in listDetails" :key="key">
          {{ item }}
        </li>
      </ul>
      <p class="mt-10 text-xs text-gray-700">
        {{ channel?.options?.PRE_HOME_NOTATION }}
      </p>
    </div>

    <div class="flex justify-around pb-2 text-center text-xs">
      <a
        v-if="channelDocuments?.legalTerms"
        :href="channelLegalTermsLink"
        target="_blank"
        class="mr-4 text-gray-400"
        @click="sendGaEvent('click_prehome_mentions')"
      >
        Mentions légales
      </a>
      <a
        v-if="channelDocuments?.privacyPolicy"
        :href="channelPrivacyPolicyLink"
        target="_blank"
        class="mr-4 text-gray-400"
        @click="sendGaEvent('click_prehome_polconf')"
      >
        Politique de confidentialité
      </a>
      <a
        v-if="channelDocuments?.generalTermsOfUse"
        :href="channelGeneralTermsOfUseLink"
        target="_blank"
        class="text-gray-400"
        @click="sendGaEvent('click_prehome_cgu')"
      >
        Conditions Générales d'Utilisation
      </a>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { computed } from 'vue'

import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const {
  channel,
  channelDocuments,
  channelGeneralTermsOfUseLink,
  channelLegalTermsLink,
  channelPrivacyPolicyLink,
} = storeToRefs(useChannelStore())

const listDetails = computed(() => {
  return channel.value?.options?.PRE_HOME_LIST
    ? channel.value?.options?.PRE_HOME_LIST.split(';')
    : ''
})
</script>
