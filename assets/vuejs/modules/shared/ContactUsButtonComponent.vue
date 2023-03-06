<template>
  <div class="right-0 float-right hidden lg:flex">
    <RouterLink
      :to="{name: PageList.CONTACT_PAGE}"
      class="button button-secondary">
      <MailIconLightComponent class="mr-1" />
      <PhoneLightIconComponent class="mr-1" />
      Contactez-nous
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import { PageList } from '@/vuejs/router'
</script>
