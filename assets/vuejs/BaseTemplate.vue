<template>
  <div  class="min-h-screen">
    <div>
      <button class="fixed top-[280px] bg-purple-600 h-[51px] w-12 text-white right-0 rounded-l-[30px] hover:w-20">
        <i class="fa fa-envelope text-lg" />
      </button>
    </div>
    <div>
      <button class="fixed top-[354px] bg-purple-600 h-[50px] w-12 text-white right-0 rounded-l-[30px] hover:w-20">
        <i class="fa fa-phone text-lg" />
      </button>
    </div>
    <HeaderSharedComponent />
    <main
        class="flex-1"
    >
      <slot />
    </main>
    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@vueuse/head'
import {computed, onMounted} from 'vue'
import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
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

