<template>
  <div class="fixed left-full top-[280px] z-50 hidden flex-col sm:flex">
    <RouterLink
      :to="{ name: PageList.CONTACT_PAGE }"
      class="sticky-right-button flex items-center self-start hover:right-[265px]"
    >
      <MailIconComponent class="mr-4" />
      <span>{{ MAIL_ANIMATION }}</span>
    </RouterLink>
    <a
      :href="`tel:${PHONE_ANIMATION}`"
      class="sticky-right-button mt-4 flex items-center self-start hover:right-[195px]"
    >
      <PhoneIconComponent class="mr-4" />
      <span>{{ PHONE_ANIMATION }}</span>
    </a>
  </div>
</template>

<script lang="ts" setup>
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import { MAIL_ANIMATION, PHONE_ANIMATION } from '../../services/const'
import { PageList } from '@/vuejs/router'
</script>

<style lang="postcss">
.sticky-right-button {
  @apply relative right-[56px] h-[50px] rounded-l-full bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2
  px-5 text-white transition-all;
}
</style>
