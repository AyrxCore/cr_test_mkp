<template>
  <div class="flex items-center justify-center lg:min-w-[200px]">
    <RouterLink
      :to="{ name: PageList.HOME_PAGE }"
      class="flex justify-center text-xl font-bold"
      @click="
        sendGtmEvent('logo_click', {
          origin_url: router.currentRoute.value.fullPath,
        })
      "
    >
      <div class="flex max-w-[125px] items-center md:max-w-[175px]">
        <img :src="primaryLogo" alt="Logo" />
      </div>
      <div
        v-if="adherentLogoImg"
        class="ml-1 mr-2 flex max-w-[125px] items-center md:max-w-[175px] lg:mr-0"
      >
        <img :src="adherentLogoImg" alt="Logo Adhérent" />
      </div>
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import router, { PageList } from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
import { getImage } from '@/vuejs/services/utils'
import { sendGtmEvent } from '@/vuejs/services/gtm'

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
