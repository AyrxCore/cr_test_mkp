<template>
  <div class="hidden sm:block">
    <button class="sticky-right-button top-[280px]">
      <MailIconComponent />
    </button>
    <button class="sticky-right-button top-[340px]">
      <PhoneIconComponent />
    </button>
  </div>
</template>

<script lang="ts" setup>
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue'
</script>

<style lang="postcss">
.sticky-right-button {
  @apply fixed -right-[190px] z-50 h-[50px] w-[245px] rounded-l-full bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2
  pl-5 text-white;
  &:hover {
    @apply right-0;
  }
}
</style>
