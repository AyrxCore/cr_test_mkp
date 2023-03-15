<template>
  <div class="hidden sm:block">
    <RouterLink
      :to="{ name: PageList.CONTACT_PAGE }"
      class="sticky-right-button top-[280px] flex items-center justify-between pr-1"
    >
      <MailIconComponent />
      <span>{{ MAIL_ANIMATION }}</span>
    </RouterLink>
    <button class="sticky-right-button top-[340px] flex items-center justify-between pr-1">
      <PhoneIconComponent />
      <span>{{ PHONE_ANIMATION }}</span>
    </button>
  </div>
</template>

<script lang="ts" setup>
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
import { MAIL_ANIMATION, PHONE_ANIMATION } from '../../services/const';
import { PageList } from '@/vuejs/router';
</script>

<style lang="postcss">
.sticky-right-button {
  @apply fixed -right-[205px] z-50 h-[50px] w-[260px] rounded-l-full bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2
  pl-5 text-white;
  &:hover {
    @apply right-0;
  }
}
</style>
