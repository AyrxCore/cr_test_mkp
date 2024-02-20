<template>
  <div
    v-if="
      channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.PROMOTIONAL_FAT) &&
      properties.fat_promo_img &&
      properties.fat_promo_txt
    "
    class="flex flex-col items-center rounded-md bg-secondary p-6 lg:flex-row"
    :class="'text-' + betterTextColor('secondary')"
  >
    <div class="flex max-w-screen-md items-center justify-center lg:w-5/12">
      <img :src="properties.fat_promo_img" alt="Image promo" />
    </div>
    <div class="my-8 flex flex-col justify-center lg:w-7/12 lg:pl-6">
      <h3 class="mb-8 text-3xl font-bold">{{ properties.fat_promo_titre }}</h3>
      <div class="mb-8 text-lg" v-html="properties.fat_promo_txt" />
      <ButtonComponent
        class="button-primary-outline mx-auto"
        @click="openingNewTab(properties.fat_promo_cta_link)"
      >
        <span class="w-full">{{ properties.fat_promo_cta_txt }}</span>
      </ButtonComponent>
    </div>
  </div>
</template>

<script setup lang="ts">
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { betterTextColor, openInNewTab } from '@/vuejs/services/utils'
import { useChannelStore } from '@/vuejs/stores/channel'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'

const props = defineProps({
  properties: {
    type: Object,
    default: null,
  },
})

const channelStore = useChannelStore()

const openingNewTab = (url: string) => {
  openInNewTab(formatUrlWithChannelCode(url))
}
</script>
