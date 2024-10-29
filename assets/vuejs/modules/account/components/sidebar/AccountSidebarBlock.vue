<template>
  <div class="rounded-lg bg-white xl:py-7 xl:pl-7 xl:pr-4">
    <h3 class="mb-2 text-lg font-bold text-primary xl:text-2xl">
      {{ props.title }}
    </h3>
    <p
      v-for="(item, keyItem) in props.items"
      :key="keyItem"
      class="flex items-center md:text-base xl:mb-3"
    >
      <AccountSidebarBlockItem
        :item="item"
        @click-link="sendGaEvent(item.gaEventName)"
      />
    </p>
  </div>
</template>
<script lang="ts" setup>
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

import AccountSidebarBlockItem from '@/vuejs/modules/account/components/sidebar/AccountSidebarBlockItem.vue'

const props = defineProps({
  title: {
    required: true,
    type: String,
  },
  items: {
    required: true,
    type: Array,
  },
})
</script>
