<template>
  <div v-if="properties.img_rse && properties.texte_rse" class="px-6">
    <div
      class="mt-5 flex flex-col items-center justify-center lg:flex-row-reverse"
    >
      <div
        class="flex max-w-screen-md justify-center rounded-lg px-2 lg:ml-10 lg:items-center lg:border lg:px-0"
      >
        <img :src="properties.img_rse" alt="Picture" class="object-cover" />
      </div>
      <div
        class="mt-5 flex flex-col rounded-lg p-5 text-lg lg:ml-6 lg:mt-0 lg:w-1/2 lg:pr-12"
      >
        <h3 class="mb-6 text-3xl font-bold text-primary">Engagement RSE</h3>

        <p class="mb-5" v-html="properties.texte_rse" />
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const props = defineProps({
  properties: {
    type: Object,
    default: null,
  },
})
</script>

<style scoped></style>
