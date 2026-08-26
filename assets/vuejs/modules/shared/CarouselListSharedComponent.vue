<template>
  <Swiper
    :class="{ 'overflow-visible': overflowVisible }"
    :modules="defaultModules"
    :navigation="{
      enabled: showNav,
      prevEl: '.swiper-button-direction-prev',
      nextEl: '.swiper-button-direction-next',
    }"
    :pagination="{
      enabled: pagination,
      clickable: true,
      bulletClass: 'swiper-pagination-bullet',
      bulletActiveClass: 'swiper-pagination-bullet-active',
    }"
    :breakpoints="breakpoints"
    :slides-per-view="slidesPerView"
    :space-between="spaceBetween"
    :round-lengths="true"
    @swiper="emit('on-swipe')"
    @slide-change="emit('on-slide-change')"
  >
    <slot />
    <template v-if="showNav">
      <button class="swiper-button-direction-prev" @click="$emit('click-left')">
        <ChevronLeftIconComponent />
      </button>
      <button
        class="swiper-button-direction-next"
        @click="$emit('click-right')"
      >
        <ChevronLeftIconComponent />
      </button>
    </template>
  </Swiper>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { Swiper } from 'swiper/vue'
import {
  Navigation,
  Pagination,
  Scrollbar,
  A11y,
  Thumbs,
  Autoplay,
} from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/a11y'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/scrollbar'
import 'swiper/css/thumbs'

import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'

const emit = defineEmits([
  'on-swipe',
  'on-slide-change',
  'click-left',
  'click-right',
])

const props = defineProps({
  modules: {
    required: false,
    type: Array,
    default: undefined,
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
  breakpoints: {
    required: false,
    type: Object,
    default: undefined,
  },
  slidesPerView: {
    required: false,
    type: Number,
    default: 1,
  },
  spaceBetween: {
    required: false,
    type: Number,
    default: 0,
  },
  overflowVisible: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const defaultModules = computed(
  () =>
    props.modules ?? [
      Navigation,
      Pagination,
      Scrollbar,
      A11y,
      Thumbs,
      Autoplay,
    ],
)
</script>

<style>
@reference '@/style/main.css';

.swiper-slide {
  img {
    @apply max-h-full;
  }
}

.swiper-pagination-bullet {
  @apply h-[0.625rem] w-[0.625rem] cursor-pointer border border-secondary bg-white opacity-100;

  &.swiper-pagination-bullet-active {
    @apply bg-secondary;
  }
}

.swiper-button-prev,
.swiper-button-next {
  &:after {
    @apply text-2xl text-primary;
  }
}

.swiper {
  position: unset;
}

.swiper-button-direction-prev,
.swiper-button-direction-next {
  @apply absolute bottom-0 top-0 z-10 flex cursor-pointer items-center text-primary;

  svg {
    @apply h-8 w-8 rounded-full border-2 border-primary bg-white;
  }
}

.swiper-button-direction-prev {
  @apply left-[-16px];
}

.swiper-button-direction-next {
  @apply right-[-16px];

  svg {
    @apply rotate-180;
  }
}

.swiper-button-disabled {
  opacity: 0.2;
}

.swiper-button-lock {
  display: none;
}

.swiper-nav-outside {
  .swiper-button-direction-prev {
    @apply md:left-[-48px];
  }
  .swiper-button-direction-next {
    @apply md:right-[-48px];
  }
}

.nav-mobile-only .swiper-button-direction-prev,
.nav-mobile-only .swiper-button-direction-next {
  @apply flex xl:hidden;
}
</style>
