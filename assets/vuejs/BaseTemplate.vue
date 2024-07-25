<template>
  <div class="flex min-h-screen flex-col">
    <StickyContactButtons />
    <HeaderSharedComponent />

    <div
      v-if="
        banner &&
        channelStore.isAllowedToShow(
          OPTIONAL_FRONT_BLOCKS.BANNER_FLASH_HOMEPAGE,
        )
      "
      class="bg-primary p-4 text-center"
      :style="{
        color: betterTextColor('primary'),
      }"
    >
      <p class="text-sm md:w-auto md:text-base lg:text-lg">
        <span class="mr-0 lg:mr-2">
          {{ banner.text }}
        </span>
        <a :href="banner.ctaLink" class="underline">
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
        class="rotate-180"
        :style="{
          color: betterTextColor('secondary'),
        }"
      />
    </div>

    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@unhead/vue'
import { onBeforeUnmount, onMounted, reactive } from 'vue'
import { storeToRefs } from 'pinia'

import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'

import { useBannerStore } from '@/vuejs/stores/banner'
import { useChannelStore } from '@/vuejs/stores/channel'
import { betterTextColor } from '@/vuejs/services/utils'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { useUserStore } from '@/vuejs/stores/user'

const channelStore = useChannelStore()
const { channel } = storeToRefs(channelStore)
const bannerStore = useBannerStore()
const { banner } = storeToRefs(bannerStore)
const userStore = useUserStore()
let broadcastChannel = null

const props = defineProps({
  title: {
    required: false,
    type: String,
    default: '',
  },
})

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  if (userStore.isNeoAutoLogin) {
    broadcastChannel = new BroadcastChannel('logout_channel')
    broadcastChannel.onmessage = handleLogoutMessage
    window.addEventListener('beforeunload', handleBeforeUnload)
  }
})

onBeforeUnmount(() => {
  if (broadcastChannel) {
    broadcastChannel.close()
    window.removeEventListener('beforeunload', handleBeforeUnload)
  }
})

const scTimer = reactive({ value: null })
const scY = reactive({ value: 0 })
const pageTitle = reactive({
  value:
    (props.title.length && `${props.title} | ${channel.value.name}`) ||
    channel.value.name,
})

const handleScroll = () => {
  if (scTimer.value) return
  scTimer.value = setTimeout(() => {
    scY.value = window.scrollY
    clearTimeout(scTimer.value)
    scTimer.value = 0
  }, 100)
}

const handleBeforeUnload = (event) => {
  broadcastChannel.postMessage('logout')
  handleLogout()
}

const handleLogoutMessage = () => {
  handleLogout()
  broadcastChannel.close()
}

const handleLogout = async () => {
  if (userStore.isLogged) {
    await userStore.logout()
    window.location.reload()
  }
}
const toTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}
useHead({
  title: pageTitle.value,

  meta: [
    {
      property: 'og:title',
      content: props.title,
    },
  ],
})
</script>
