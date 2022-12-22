<template>
  <div class="right-0 float-right hidden lg:flex">
    <ButtonComponent class="button-secondary">
      <MailIconLightComponent class="mr-1" />
      <PhoneLightIconComponent class="mr-1" />
      Contactez-nous
    </ButtonComponent>
  </div>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import MailIconLightComponent from '@/vuejs/modules/shared/icon/MailIconLightComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
</script>
