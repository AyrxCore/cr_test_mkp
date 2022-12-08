<template>
  <Swiper
    :modules="defaultModules"
    :space-between="spaceBetween"
    :loop="loop"
    :navigation="navigation"
    :pagination="pagination"
    :slides-per-view="slidesPerPerView"
    class="mx-auto"
    @swiper="emit('on-swipe')"
    @slide-change="emit('on-slide-change')"
  >
    <slot />
  </Swiper>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { Swiper } from 'swiper/vue'
import { Navigation, Pagination, Scrollbar, A11y, Thumbs } from 'swiper'
import 'swiper/scss'
import 'swiper/scss/a11y'
import 'swiper/scss/navigation'
import 'swiper/scss/pagination'
import 'swiper/scss/scrollbar'
import 'swiper/scss/thumbs'

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
    type: [Boolean, Object],
    default: false,
  },
})

const defaultModules = computed(
  () => props.modules ?? [Navigation, Pagination, Scrollbar, A11y, Thumbs],
)
</script>

<style lang="postcss">
.swiper-slide {
  img {
    @apply max-h-full;
  }
}

.swiper-pagination-bullet {
  @apply h-[0.625rem] w-[0.625rem] border border-secondary bg-white opacity-100;
  &-active {
    @apply bg-secondary;
  }
}

.swiper-button {
  &-prev,
  &-next {
    &:after {
      @apply text-2xl text-primary;
    }
  }
}
</style>
