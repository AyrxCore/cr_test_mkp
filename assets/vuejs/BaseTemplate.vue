<template>
  <div class="min-h-screen">
    <StickyContactButtons />
    <HeaderSharedComponent />
    <div
      class="bg-gradient flex h-[59px] flex-row items-center justify-center py-4 text-white"
    >
      <p class="w-[305px] text-sm md:w-auto md:text-base lg:text-lg flex flex-col items-center lg:flex-row py-2">
        <span class="mr-0 lg:mr-2">
          Cybersécurité : protégez votre entreprise et vos salariés. Téléchargez notre guide.
        </span>
        <RouterLink
          to="/app/actualite/guide-cybersecurite"
          class="underline"
        >
          Découvrir
        </RouterLink>
      </p>

      <!--<button class="absolute right-2 text-white">
        <CloseIconComponent />
      </button>-->
    </div>
    <main class="">
      <slot />
    </main>

    <div v-show="scY.value > 500" id="pagetop" class="fixed right-1 bottom-10 p-1 bg-secondary rounded cursor-pointer z-10" @click="toTop">
      <ChevronDownIconComponent class="stroke-white rotate-180" />
    </div>
    <FooterSharedComponent />
  </div>
</template>

<script lang="ts" setup>
import { useHead } from '@vueuse/head'
import { computed, onMounted, reactive, ref } from 'vue'
import HeaderSharedComponent from '@/vuejs/modules/shared/HeaderSharedComponent.vue'
import FooterSharedComponent from '@/vuejs/modules/shared/FooterSharedComponent.vue'
import StickyContactButtons from '@/vuejs/modules/shared/StickyContactButtonsComponent.vue'
import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import {useUserStore} from "@/vuejs/stores/user";
import {storeToRefs} from "pinia";
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue';
import { URL_HOME_BANDEAU } from '@/vuejs/services/utils';
const userStore = useUserStore()
const { user } = storeToRefs(userStore)
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


const handleScroll = (() => {
  if (scTimer.value) return
  scTimer.value = setTimeout(() => {
    scY.value = window.scrollY
    clearTimeout(scTimer.value)
    scTimer.value = 0
  }, 100)
})

const toTop = (() => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
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
