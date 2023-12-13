<template>
  <p class="mb-3 flex items-center">
    <ChevronRightIconComponent :stroke-color="'#5E6875'" />
    <RouterLink :to="{ name: props.item.url }">
      <span
        class="text-sm text-gray-500 underline decoration-2 underline-offset-4 hover:decoration-purple-600 md:text-base"
        :class="isActive ? 'route-active' : ''"
      >
        {{ item.name }}
      </span>
    </RouterLink>
  </p>
</template>
<script lang="ts" setup>
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { onMounted, ref } from 'vue'
import router from '@/vuejs/router'

const isActive = ref<boolean>(false)
const props = defineProps({
  item: {
    required: true,
    type: Object,
  },
})

onMounted(() => {
  const parentUrl = router.resolve({ name: props.item.url }).href
  const currentUrl = window.location.href
  isActive.value = currentUrl.includes(parentUrl)
})
</script>
<style scoped>
.route-active {
  @apply text-secondary decoration-secondary;
}
</style>
