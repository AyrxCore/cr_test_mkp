<template>
  <div class="container mx-auto my-10 mt-[5%] px-5 text-primary lg:px-0">
    <div v-if="content" v-html="content"></div>
    <LoadingComponent v-else />
  </div>
</template>

<script lang="ts" setup>
import { useCmsStore } from '@/vuejs/stores/cms'
import { onMounted, ref } from 'vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const cmsStore = useCmsStore()
const content = ref()

const props = defineProps({
  pageId: {
    type: Number,
    required: true,
  },
})

onMounted(async (): Promise<void> => {
  content.value = await cmsStore.getPageById(props.pageId)
})
</script>

<style scoped>
.mentions-legales p {
  @apply text-justify !text-sm md:!text-base lg:!text-lg;
}
</style>
