<template>
  <div
    class="fixed left-full top-[280px] z-50 hidden flex-col font-cotext sm:flex"
  >
    <RouterLink
      ref="emailButton"
      :to="contactPage"
      class="sticky-right-button button-primary button-email flex items-center self-start border-none"
      :style="channelEmailButtonStyle"
      @mouseover.once="getEmailButtonWidth"
      @click="gtmEvent('click_widget_email')"
    >
      <MailIconComponent
        class="mr-4"
        :size-width="21.21"
        :size-height="17.36"
        :style="{
          fill: betterTextColor('primary'),
        }"
      />
      <span>{{ channel?.email }}</span>
    </RouterLink>
    <a
      :href="`tel:${channel?.phoneNumber}`"
      class="button-primary sticky-right-button mt-4 flex items-center self-start border-none hover:right-[195px]"
      @click="gtmEvent('click_widget_tel')"
      @mouseover.once="gtmEvent('hover_widget_tel')"
    >
      <PhoneIconComponent
        class="mr-4"
        :style="{
          fill: betterTextColor('primary'),
        }"
      />
      <span>{{ channelPhoneNumber }}</span>
    </a>
  </div>
</template>

<script lang="ts" setup>
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import { PageList } from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { computed, onUpdated, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { betterTextColor } from '@/vuejs/services/utils'

const userStore = useUserStore()
const channelStore = useChannelStore()
const { channel, formattedPhoneNumber: channelPhoneNumber } =
  storeToRefs(channelStore)
const emailButton = ref(null)
const emailButtonWidth = ref(null)

const currentChannel = channelStore.currentChannel

const contactPage = computed(() => ({
  name: PageList.CONTACT_PAGE,
}))

function getEmailButtonWidth() {
  if (!emailButton.value.$el) {
    return
  }

  emailButtonWidth.value = emailButton.value.$el.clientWidth
  gtmEvent('hover_widget_email')
}

onUpdated(() => {
  emailButtonWidth.value = document.querySelector('.button-email').clientWidth
})

const channelEmailButtonStyle = computed(() => {
  return {
    '--email-right': `${emailButtonWidth.value || 100}px`,
  }
})

const gtmEvent = (eventName: string) => {
  gtmMixinPushEvent(
    eventName,
    buildStandardGtmData(userStore.user['@id'], currentChannel.name),
  )
}
</script>

<style lang="postcss">
.sticky-right-button {
  @apply relative right-[56px] h-[45px] rounded-l-[20px] px-5 text-white transition-all;

  &.button-email {
    text-wrap: nowrap;

    &:hover {
      /*noinspection CssUnresolvedCustomProperty*/
      right: var(--email-right);
    }
  }
}
</style>
