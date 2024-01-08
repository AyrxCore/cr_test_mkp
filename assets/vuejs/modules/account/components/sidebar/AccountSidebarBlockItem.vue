<template>
  <p class="mb-3 flex items-center">
    <ChevronRightIconComponent
      v-if="isActive"
      :stroke="channelPrimaryColor"
      :width="40"
      :height="30"
    />
    <RouterLink :to="{ name: props.item.url }" @click="emit('clickLink')">
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
import { click } from 'dom7'

const isActive = ref<boolean>(false)
const props = defineProps({
  item: {
    required: true,
    type: Object,
  },
})

const emit = defineEmits(['clickLink'])

const { channelPrimaryColor } = storeToRefs(useChannelStore())

onMounted(() => {
  const parentUrl = router.resolve({ name: props.item.url }).href
  const currentUrl = window.location.href
  isActive.value = currentUrl.includes(parentUrl)
})
</script>
