<template>
  <div class="min-h-screen">
    <div>
      <button class="top-[280px] sticky-left-button" >
        <MailIconComponent />
      </button>
    </div>
    <div>
      <button class="top-[354px] sticky-left-button">
        <PhoneIconComponent />
      </button>
    </div>
    <HeaderSharedComponent />
    <main class="">
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
import MailIconComponent from '@/vuejs/modules/shared/icon/MailIconComponent.vue';
import PhoneIconComponent from '@/vuejs/modules/shared/icon/PhoneIconComponent.vue';

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
