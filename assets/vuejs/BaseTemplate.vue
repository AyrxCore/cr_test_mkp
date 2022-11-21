<template>
  <div class="min-h-screen">
    <div>
      <button
        class="fixed top-[280px] right-0 h-[51px] w-12 rounded-l-[30px] bg-purple-600 text-white hover:w-20"
      >
        <i class="fa fa-envelope text-lg" />
      </button>
    </div>
    <div>
      <button
        class="fixed top-[354px] right-0 h-[50px] w-12 rounded-l-[30px] bg-purple-600 text-white hover:w-20"
      >
        <i class="fa fa-phone text-lg" />
      </button>
    </div>
    <HeaderSharedComponent />
    <main class="m-auto my-4 max-w-screen-2xl flex-1">
      <slot />
    </main>
    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@vueuse/head'
import { computed, onMounted } from 'vue'
import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'

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
