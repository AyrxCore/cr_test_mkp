<template>
  <div class="flex min-h-screen flex-col">
    <HeaderSharedComponent />

    <!-- TODO: TEMP MKP-1447 — à supprimer après le 28/06/2026 -->
    <div
      :style="{ color: betterTextColor('primary') }"
      class="bg-primary p-4 text-center"
    >
      <p class="text-sm md:w-auto md:text-base lg:text-lg">
        Votre Marketplace fait peau neuve ! Les commandes en ligne seront temporairement suspendues du 19 juin (16h) au 29 juin (12h). Merci de votre compréhension.
      </p>
    </div>
    <!-- END TEMP MKP-1447 -->

    <div
      v-if="
        banner &&
        channelStore.isAllowedToShow(
          OPTIONAL_FRONT_BLOCKS.BANNER_FLASH_HOMEPAGE,
        )
      "
      :style="{ color: betterTextColor('primary') }"
      class="bg-primary p-4 text-center"
    >
      <p class="text-sm md:w-auto md:text-base lg:text-lg">
        <span class="mr-0 lg:mr-2">
          {{ banner.text }}
        </span>
        <a
          :href="banner.ctaLink"
          class="underline"
          @click="
            sendGtmEvent('flash_banner_click', {
              link_text: $event.target.innerText,
              link_url: banner.ctaLink,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          {{ banner.ctaTxt }}
        </a>
      </p>
    </div>

    <main class="flex-grow">
      <slot />
    </main>

    <div
      v-show="scY.value > 500"
      id="pagetop"
      class="fixed bottom-10 right-1 z-10 cursor-pointer rounded bg-secondary p-1"
      @click="toTop"
    >
      <ChevronDownIconComponent
        :style="{ color: betterTextColor('secondary') }"
        class="rotate-180"
      />
    </div>

    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onMounted, reactive } from 'vue'
import { storeToRefs } from 'pinia'
import { useHead } from '@unhead/vue'

import router from '@/vuejs/router'
import { useBannerStore } from '@/vuejs/stores/banner'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { betterTextColor } from '@/vuejs/services/utils'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'

const channelStore = useChannelStore()
const { channel } = storeToRefs(channelStore)
const bannerStore = useBannerStore()
const { banner } = storeToRefs(bannerStore)
const userStore = useUserStore()
let broadcastChannel = null

const props = defineProps({
  title: { required: false, type: String, default: '' },
})

onBeforeMount(() => {
  sendGtmEvent('datalayer_ready', {
    marketplace: channelStore.currentChannel.name,
    user_id: userStore.user.id,
  })
  if (userStore.isNeoAutoLogin) {
    broadcastChannel = new BroadcastChannel('logout_channel')
    broadcastChannel.onmessage = handleLogoutMessage
    window.addEventListener('beforeunload', handleBeforeUnload)
  }
})

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

const scTimer = reactive({ value: null })
const scY = reactive({ value: 0 })

const pageTitle = computed((): string => {
  return (
    (props.title.length && `${props.title} | ${channel.value.name}`) ||
    channel.value.name
  )
})

useHead({
  title: pageTitle,
  meta: computed(() => [{ property: 'og:title', content: pageTitle.value }]),
})

const handleScroll = () => {
  if (scTimer.value) return
  scTimer.value = setTimeout(() => {
    scY.value = window.scrollY
    clearTimeout(scTimer.value)
    scTimer.value = 0
  }, 100)
}

const toTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const handleBeforeUnload = (event) => {
  navigator.sendBeacon('/api/user/logout')
  broadcastChannel.postMessage('logout')
}

const handleLogoutMessage = async () => {
  await handleLogout()
}

const handleLogout = async () => {
  if (userStore.isLogged) {
    await userStore.logout()
    broadcastChannel.close()
    window.location.reload()
  }
}
</script>
