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

    <div class="flex justify-between pb-2 text-center text-xs">
      <a
        :href="channel?.documents.legalTerms"
        target="_blank"
        class="mr-4 text-gray-400"
        >Mentions légales</a
      >
      <a
        :href="channel?.documents.privacyPolicy"
        target="_blank"
        class="mr-4 text-gray-400"
        >Politique de confidentialité</a
      >
      <a
        :href="channel?.documents.generalTermsOfUse"
        target="_blank"
        class="text-gray-400"
        >Conditions Générales d'Utilisation</a
      >
    </div>
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'
import { computed } from 'vue'

const { channel } = storeToRefs(useChannelStore())
const listDetails = computed(() => {
  return channel.value?.options?.PRE_HOME_LIST
    ? channel.value?.options?.PRE_HOME_LIST.split(';')
    : ''
})
</script>
