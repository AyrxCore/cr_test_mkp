<template>
  <RouterLink
    :style="{
      color: betterTextColor('primary'),
    }"
    :to="{ name: PageList.CONTACT_PAGE }"
    class="button button-primary !px-4 !text-lg"
    @click="
      sendGtmEvent('contact_click', {
        position: 'header',
        link_text: $event.target.innerText,
        link_url: router.resolve({
          name: PageList.CONTACT_PAGE,
        }).fullPath,
        origin_url: router.currentRoute.value.fullPath,
      })
    "
  >
    Nous contacter
    <PhoneIconComponent
      :fill="betterTextColor('primary')"
      class="ml-2"
      width="19"
    />
    <MailIconComponent
      :fill="betterTextColor('primary')"
      class="ml-2 mr-0"
      width="19"
    />
  </RouterLink>
</template>

<script lang="ts" setup>
import router, { PageList } from '@/vuejs/router'
import { betterTextColor } from '@/vuejs/services/utils'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
</script>
