<template>
  <div class="absolute inset-0 bg-black bg-opacity-40">
    <div
      class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transform"
    >
      <ButtonComponent
        :is-loading="isLoading"
        class="button-primary-outline-white"
        @click="geolocateMe"
      >
        <LocationIconComponent class="mr-2" />
        Me géolocaliser
      </ButtonComponent>
    </div>
  </div>
</template>

<script lang="ts" setup>
import router from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LocationIconComponent from '@/vuejs/modules/shared/icon/LocationIconComponent.vue'

defineProps({
  isLoading: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['geolocation-request'])

const geolocateMe = () => {
  emit('geolocation-request')
  sendGtmEvent('geolocate_me_click', {
    origin_url: router.currentRoute.value.fullPath,
  })
}
</script>
