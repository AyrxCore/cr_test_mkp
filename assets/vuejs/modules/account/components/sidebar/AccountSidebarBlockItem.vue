<template>
  <p class="mb-3 flex items-center">
    <ChevronRightIconComponent
      v-if="isActive"
      :stroke="channelPrimaryColor"
      :size-width="40"
      :size-height="30"
    />
    <RouterLink :to="{ name: props.item.url }">
      <span
        class="text-sm text-primary underline decoration-2 underline-offset-4 hover:font-bold md:text-base xl:text-lg"
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
import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'

const isActive = ref<boolean>(false)
const props = defineProps({
  item: {
    required: true,
    type: Object,
  },
})

const { channelPrimaryColor } = storeToRefs(useChannelStore())

onMounted(() => {
  const parentUrl = router.resolve({ name: props.item.url }).href
  const currentUrl = window.location.href
  isActive.value = currentUrl.includes(parentUrl)
})
</script>
