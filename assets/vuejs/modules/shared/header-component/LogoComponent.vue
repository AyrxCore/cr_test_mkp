<template>
  <div class="mr-4 flex items-center justify-center lg:min-w-[240px]">
    <RouterLink
      :to="{ name: PageList.HOME_PAGE }"
      class="flex justify-center text-xl font-bold"
      @click="sendGaEvent('click_header_logo')"
    >
      <div class="flex max-w-[125px] items-center md:max-w-[175px]">
        <img :src="primaryLogo" alt="Logo" />
      </div>
      <div
        v-if="adherentLogoImg"
        class="ml-1 flex max-w-[125px] items-center md:max-w-[175px]"
      >
        <img :src="adherentLogoImg" alt="Logo Adhérent" />
      </div>
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
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const channelStore = useChannelStore()
const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const primaryLogo = getImage(channelStore.channel.design.logo)

const adherentLogoImg = computed(() => {
  const logo = user.value.account?.adherent.logo
  if (!logo) return null
  return getImage(logo)
})
</script>
