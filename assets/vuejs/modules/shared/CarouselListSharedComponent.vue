<template>
  <swiper
    :style="{
      '--swiper-navigation-color': '#050056',
    }"
    :modules="defaultModules"
    :space-between="spaceBetween"
    :loop="true"
    :navigation="navigation"
    :pagination="pagination"
    :slides-per-view="slidesPerPerView"
    class="mx-auto items-center text-center"
    @swiper="emit('on-swipe')"
    @slide-change="emit('on-slide-change')"
  >
    <slot />
  </swiper>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { Swiper } from 'swiper/vue'
import { Navigation, Pagination, Scrollbar, A11y, Thumbs } from 'swiper'
import 'swiper/swiper.min.css'
import 'swiper/css/bundle'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/scrollbar'

const emit = defineEmits(['on-swipe', 'on-slide-change'])

const props = defineProps({
  slidesPerPerView: {
    required: false,
    type: Number,
    default: 4,
  },
  spaceBetween: {
    required: false,
    type: Number,
    default: 50,
  },
  modules: {
    required: false,
    type: Array,
  },
  loop: {
    required: false,
    type: Boolean,
    default: true,
  },
  pagination: {
    required: false,
    type: Boolean,
    default: false,
  },
  navigation: {
    required: false,
    type: Boolean,
    default: true,
  },
})

const defaultModules = computed(
  () => props.modules ?? [Navigation, Pagination, Scrollbar, A11y, Thumbs],
)
</script>
