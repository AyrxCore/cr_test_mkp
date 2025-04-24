<template>
  <div class="absolute inset-0 bg-black bg-opacity-40">
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transform">
      <ButtonComponent
        class="button-primary-outline-white"
        :is-loading="isLoading"
        @click="$emit('geolocation-request')"
      >
        <LocationIconComponent class="mr-2" />
        Me géolocaliser
      </ButtonComponent>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LocationIconComponent from '@/vuejs/modules/shared/icon/LocationIconComponent.vue'

defineProps({
  isLoading: {
    type: Boolean,
    required: true,
  },
})

defineEmits(['geolocation-request'])
</script>
