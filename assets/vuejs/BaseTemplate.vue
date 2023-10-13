<template>
  <div class="min-h-screen">
    <StickyContactButtons />
    <HeaderSharedComponent />
    <div
      v-if="banner"
      class="bg-gradient flex h-[59px] flex-row items-center justify-center py-4 text-white"
    >
      <p
        class="flex w-[305px] flex-col items-center py-2 text-sm md:w-auto md:text-base lg:flex-row lg:text-lg"
      >
        <span class="mr-0 lg:mr-2">
          {{ banner.text }}
        </span>
        <a :href="banner.ctaLink" class="underline">
          {{ banner.ctaTxt }}
        </a>
      </p>
    </div>
    <main class="">
      <slot />
    </main>

    <div
      v-show="scY.value > 500"
      id="pagetop"
      class="fixed right-1 bottom-10 z-10 cursor-pointer rounded bg-secondary p-1"
      @click="toTop"
    >
      <ChevronDownIconComponent class="rotate-180 stroke-white" />
    </div>
    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@vueuse/head'
import { computed, onMounted, reactive } from 'vue'
import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import { storeToRefs } from 'pinia'
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'
import { useBannerStore } from '@/vuejs/stores/banner'

const expertContentStore = useBannerStore()
const { banner } = storeToRefs(expertContentStore)
const props = defineProps({
  title: {
    required: false,
    type: String,
    default: '',
  },
})

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

const scTimer = reactive({ value: 0 })
const scY = reactive({ value: 0 })

const handleScroll = () => {
  if (scTimer.value) return
  scTimer.value = setTimeout(() => {
    scY.value = window.scrollY
    clearTimeout(scTimer.value)
    scTimer.value = 0
  }, 100)
}

const toTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}
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
