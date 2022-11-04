<template>
  <div  class="min-h-screen">
    <HeaderSharedComponent />
    <main
        class="flex-1"
    >
      <slot />
    </main>
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@vueuse/head'
import {computed, onMounted} from 'vue'
import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import {useUserStore} from '@/vuejs/stores/user'

const userStore = useUserStore()
const props = defineProps({
  title: {
    required: false,
    type: String,
    default: '',
  },
})

useHead({
  title: computed(() => props.title),
  meta: [
    {
      property: 'og:title',
      content: props.title,
    },
  ],
})


</script>

