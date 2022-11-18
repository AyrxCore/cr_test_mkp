<template>
  <swiper
    :slides-per-view="props.slidesPerView"
    :space-between="props.spaceBetween"
    @swiper="onSwiper"
    @slideChange="onSlideChange"
  >
    <swiper-slide>Slide 1</swiper-slide>
    <swiper-slide>Slide 2</swiper-slide>
    <swiper-slide>Slide 3</swiper-slide>
  </swiper>
</template>
<script lang="ts" setup>
  import { Swiper, SwiperSlide } from 'swiper/vue'
  import 'swiper/swiper.min.css'

  const props = defineProps({
    slidesPerView: {
      required: false,
      type: Number,
      default: 3,
    },
    spaceBetween: {
      required: false,
      type: Number,
      default: 50,
    },
  })
  const onSlideChange = () => {
    console.log('slide change')
  }

  const onSwiper = (swiper) => {
    console.log(swiper)
  }
</script>
