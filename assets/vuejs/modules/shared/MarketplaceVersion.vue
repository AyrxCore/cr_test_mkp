<template>
  <div
    v-if="isAvailable"
    class="fixed bottom-0 right-0 rounded-tl-lg bg-gray-100 bg-opacity-50 p-2 italic text-red-500 text-xl font-bold"
  >
    {{ MKP_GIT_TAG }}
  </div>
</template>

<script lang="ts" setup>
const APP_MODE: string = window.APP_MODE ?? ''
const MKP_GIT_TAG: string = window.MKP_GIT_TAG ?? ''
const isAvailable: boolean = !!MKP_GIT_TAG && APP_MODE !== 'prod'
</script>
