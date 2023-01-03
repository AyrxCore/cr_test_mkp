<template>
  <div class="rounded-lg bg-white xl:py-7 xl:pl-7 xl:pr-4">
    <h3 class="mb-2 text-md font-bold text-primary xl:text-[20px]">
      {{ props.title }}
    </h3>
    <p
      v-for="(item, keyItem) in props.items"
      :key="keyItem"
      class="flex items-center xl:mb-3 text-sm md:text-base"
    >
      <AccountSidebarBlockItem :item="item"/>
    </p>
  </div>
</template>
<script lang="ts" setup>
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
