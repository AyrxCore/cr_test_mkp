<template>
  <div class="flex items-center justify-center lg:min-w-[200px]">
    <RouterLink
      :to="{ name: PageList.HOME_PAGE }"
      class="flex justify-between text-xl font-bold"
      @click="gtmEvent('click_header_logo')"
    >
      <img
        :src="primaryLogo"
        alt="Logo Qantis"
        class="left-[60px] top-[24.5px] flex h-[60px] max-w-[123px] md:max-w-[145px]"
      />
      <!--
      <img
        v-if="adherentLogoImg"
        :src="adherentLogoImg"
        alt="Logo Adhérent"
        class="left-[60px] top-[24.5px] ml-1 flex h-[60px] max-w-[123px] md:max-w-[145px]"
      /> -->
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { getImage } from '@/vuejs/services/utils'
import { PageList } from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import { computed } from 'vue'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'

const channelStore = useChannelStore()
const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const primaryLogo = getImage(channelStore.channel.design.logo)
const currentChannel = channelStore.currentChannel

const adherentLogoImg = computed(() => {
  const logo = user.value.account?.adherent.logo
  if (!logo) return null
  return getImage(logo)
})

const gtmEvent = (eventName: string) => {
  gtmMixinPushEvent(
    eventName,
    buildStandardGtmData(userStore.user['@id'], currentChannel.name),
  )
}
</script>
