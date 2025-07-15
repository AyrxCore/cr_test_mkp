<template>
  <div
    v-if="logos.length > 0"
    :class="[
      'flex w-full items-center gap-1',
      'mx-auto max-w-[220px] md:max-w-[320px]',
    ]"
  >
    <button
      v-show="hasOverflow"
      :class="[
        'flex h-5 w-5 flex-shrink-0 items-center justify-center text-sm text-primary',
        'transition-colors hover:opacity-70 disabled:cursor-not-allowed disabled:opacity-30',
      ]"
      :disabled="!canScrollLeft"
      type="button"
      @click="scrollLeft"
    >
      <ChevronLeftIconComponent
        class="h-4 w-4 stroke-current disabled:opacity-50"
      />
    </button>

    <div
      ref="logosContainer"
      :class="[
        'flex min-w-0 flex-1 overflow-x-auto scroll-smooth',
        '[-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden',
      ]"
      @scroll="updateScrollButtons"
    >
      <div class="flex items-center gap-1">
        <img
          v-for="logo in logos"
          :key="logo.id"
          :src="logo.logo"
          :alt="logo.name"
          class="h-12 w-auto flex-shrink-0 object-contain"
          @load="checkOverflow"
        />
      </div>
    </div>

    <button
      v-show="hasOverflow"
      :class="[
        'flex h-5 w-5 flex-shrink-0 items-center justify-center text-sm text-primary',
        'transition-colors hover:opacity-70 disabled:cursor-not-allowed disabled:opacity-30',
      ]"
      :disabled="!canScrollRight"
      type="button"
      @click="scrollRight"
    >
      <ChevronRightIconComponent
        class="h-4 w-4 stroke-current disabled:opacity-50"
      />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'

import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'

import { Logo } from '@/vuejs/types/Product/Logo'

const props = defineProps<{
  logos: Logo[]
}>()

const logosContainer = ref<HTMLElement>()
const canScrollLeft = ref<boolean>(false)
const canScrollRight = ref<boolean>(false)
const hasOverflow = ref<boolean>(false)

const checkOverflow = (): void => {
  if (!logosContainer.value) return

  const container: HTMLElement = logosContainer.value
  hasOverflow.value = container.scrollWidth > container.clientWidth
  updateScrollButtons()
}

const updateScrollButtons = (): void => {
  if (!logosContainer.value) return

  const container: HTMLElement = logosContainer.value
  canScrollLeft.value = container.scrollLeft > 0
  canScrollRight.value =
    container.scrollLeft < container.scrollWidth - container.clientWidth - 1
}

const scrollLeft = (): void => {
  if (!logosContainer.value) return
  logosContainer.value.scrollBy({ left: -80, behavior: 'smooth' })
}

const scrollRight = (): void => {
  if (!logosContainer.value) return
  logosContainer.value.scrollBy({ left: 80, behavior: 'smooth' })
}

let resizeObserver: ResizeObserver | null = null

onMounted((): void => {
  if (logosContainer.value && window.ResizeObserver) {
    resizeObserver = new ResizeObserver(checkOverflow)
    resizeObserver.observe(logosContainer.value)
  }

  nextTick(checkOverflow)
})

onBeforeUnmount((): void => {
  if (resizeObserver) {
    resizeObserver.disconnect()
  }
})

watch(
  () => props.logos,
  (): void => {
    nextTick(checkOverflow)
  },
  { deep: true },
)
</script>
