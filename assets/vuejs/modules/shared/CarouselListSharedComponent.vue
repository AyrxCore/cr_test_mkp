<template>
  <Swiper
    :modules="defaultModules"
    :loop="loop"
    :navigation="{
      prevEl: '.swiper-button-direction-prev',
      nextEl: '.swiper-button-direction-next',
    }"
    :pagination="pagination"
    class="mx-auto"
    @swiper="emit('on-swipe')"
    @slide-change="emit('on-slide-change')"
  >
    <slot />
    <template v-if="showNav">
      <button class="swiper-button-direction-prev">
        <ChevronLeftIconComponent />
      </button>
      <button class="swiper-button-direction-next">
        <ChevronLeftIconComponent />
      </button>
    </template>
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
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'

const emit = defineEmits(['on-swipe', 'on-slide-change'])

const props = defineProps({
  nbSlidesPerView: {
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
  showNav: {
    required: false,
    type: Boolean,
    default: true,
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

.swiper {
  position: unset;
  &-button-direction {
    &-prev,
    &-next {
      @apply absolute top-0 bottom-0 z-10 flex cursor-pointer items-center text-primary;
      svg {
        @apply h-12 w-12 rounded-full bg-white sm:bg-transparent;
      }
    }
    &-prev {
      @apply -left-4 sm:-left-14;
    }
    &-next {
      @apply -right-4 sm:-right-12;
      svg {
        @apply rotate-180;
      }
    }
  }
}
</style>
