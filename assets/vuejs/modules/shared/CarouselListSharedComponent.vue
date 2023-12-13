<template>
  <Swiper
    :modules="defaultModules"
    :navigation="{
      enabled: showNav,
      prevEl: '.swiper-button-direction-prev',
      nextEl: '.swiper-button-direction-next',
    }"
    :pagination="pagination"
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
} from 'swiper'
import 'swiper/scss'
import 'swiper/scss/a11y'
import 'swiper/scss/navigation'
import 'swiper/scss/pagination'
import 'swiper/scss/scrollbar'
import 'swiper/scss/thumbs'

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
        @apply h-8 w-8 rounded-full border-2 border-primary bg-white md:h-12 md:w-12 md:border-none md:bg-transparent;
      }
    }
    &-prev {
      @apply left-[-16px] md:left-0;
    }

    &-next {
      @apply right-[-16px] md:right-0;

      svg {
        @apply rotate-180;
      }
    }
  }
  &-button {
    &-disabled {
      opacity: 0.2;
    }
    &-lock {
      display: none;
    }
  }
  &-nav-outside {
    .swiper-button-direction {
      &-prev {
        @apply md:left-[-48px];
      }
      &-next {
        @apply md:right-[-48px];
      }
    }
  }
}

.nav-mobile-only .swiper-button-direction-prev,
.nav-mobile-only .swiper-button-direction-next {
  @apply flex xl:hidden;
}
</style>
