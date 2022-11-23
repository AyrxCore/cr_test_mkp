<template>
  <swiper
    :style="{
      '--swiper-navigation-color': '#050056',
    }"
    :modules="defaultModules"
    :space-between="props.spaceBetween"
    navigation=""
    :loop="true"
    :pagination="props.pagination"
    :slides-per-view="props.slidesPerPerView"
    @swiper="onSwiper"
    @slideChange="onSlideChange"
    class="mx-auto items-center text-center"
  >
    <slot />
  </swiper>
</template>
<script lang="ts" setup>
import { computed, onMounted } from 'vue'
import { Swiper } from 'swiper/vue'
import { Navigation, Pagination, Scrollbar, A11y } from 'swiper'
import 'swiper/swiper.min.css'
import 'swiper/css/bundle'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/scrollbar'

const props = defineProps({
  slidesPerPerView: {
    required: false,
    type: Number,
    default: 3,
  },
  mobileSlidesPerPerView: {
    required: false,
    type: Number,
    default: 2,
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
})

onMounted(()=> {
  console.log(props.mobileSlidesPerPerView)
  console.log(props.slidesPerPerView)
})
const onSlideChange = () => {
  console.log('slide change')
}

const onSwiper = (swiper) => {
  console.log(swiper.slidesPerPerView)
}

const defaultModules = computed(
  () => props.modules ?? [Navigation, Pagination, Scrollbar, A11y],
)
</script>
