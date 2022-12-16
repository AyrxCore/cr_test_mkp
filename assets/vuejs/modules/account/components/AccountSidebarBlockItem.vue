<template>
<p class="mb-3 flex flex items-center">
  <ChevronRightIconComponent :stroke-color="'#5E6875'" />
  <RouterLink
      v-slot="{isExactActive}"
      :to="{name: item.url}"
  >
    <span
      class="text-[16px] text-gray-500 underline decoration-2 underline-offset-4 hover:decoration-purple-600"
      :class="isExactActive ? 'decoration-purple-600 decoration-purple-600' : ''"
    >
      {{ item.name }}
    </span>
  </RouterLink>
</p>
</template>
<script lang="ts" setup>
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'

const props = defineProps({
  item: {
    required: true,
    type: Object
  }
})
</script>
