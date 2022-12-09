<template>
  <div class="min-h-screen">
    <StickyContactButtons />
    <HeaderSharedComponent />
    <div
      class="bg-gradient flex h-[59px] flex-row items-center justify-center py-4 text-white"
    >
      <p class="mr-0 w-[305px] text-[16px] md:mr-2 md:w-auto md:text-lg">
        Pneumatiques : êtes-vous concernés par la Loi Montagne ?
        <a href="#" class="underline">Découvrir</a>
      </p>

      <button class="absolute right-2 text-white">
        <CloseIconComponent />
      </button>
    </div>
    <main class="">
      <slot />
    </main>
    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@vueuse/head'
import { computed } from 'vue'
import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'

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
