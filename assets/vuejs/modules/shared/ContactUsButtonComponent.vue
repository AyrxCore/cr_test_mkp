<template>
  <RouterLink
    :style="{
      color: betterTextColor('primary'),
    }"
    :to="{ name: PageList.CONTACT_PAGE }"
    class="button button-primary !px-4 !text-lg"
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
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import { PageList } from '@/vuejs/router'
import { betterTextColor } from '@/vuejs/services/utils'
</script>
