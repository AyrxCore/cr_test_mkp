<template>
  <div class="rounded-lg bg-white py-7 pl-7 pr-4">
    <h3 class="primary mb-2 text-[20px]">{{ props.title }}</h3>
    <p
        v-for="(item, keyItem) in props.items"
        :key="keyItem"
        class="mb-3 flex flex items-center"
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
    type: Array
  }
})
</script>
