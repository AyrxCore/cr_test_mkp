<template>
  <Teleport to="body">
    <div
      v-if="modelValue && !hideOverlay"
      :style="{ zIndex: zIndex - 10 }"
      class="fixed inset-0 bg-black/25"
      @click="close()"
    ></div>
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <aside
        v-if="modelValue"
        :style="{ zIndex: zIndex }"
        class="fixed inset-y-0 right-0 w-full border-l border-gray-200 bg-white shadow-xl md:w-[480px]"
      >
        <div class="flex h-full flex-col">
          <!-- Header avec croix -->
          <div class="flex items-center justify-end pb-8 pr-10 pt-6">
            <button
              class="text-black transition-colors hover:text-gray-600"
              type="button"
              @click="close"
            >
              <CloseIconComponent class="h-6 w-6" />
            </button>
          </div>

          <!-- Contenu scrollable -->
          <div ref="contentRef" class="flex-1 overflow-y-auto">
            <slot></slot>

            <!-- Footer avec bouton Fermer (inline si pas de scroll) -->
            <div v-if="showCloseButton && !hasScroll" class="px-10 py-6">
              <ButtonComponent
                class="button-primary-outline w-fit text-base!"
                @click="close"
              >
                Fermer
              </ButtonComponent>
            </div>
          </div>

          <!-- Footer avec bouton Fermer (fixe si scroll) -->
          <div v-if="showCloseButton && hasScroll" class="px-10 py-6">
            <ButtonComponent
              class="button-primary-outline w-fit text-base!"
              @click="close"
            >
              Fermer
            </ButtonComponent>
          </div>
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>

<script lang="ts" setup>
import { watch, ref, onMounted, onUnmounted, nextTick } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  showCloseButton: {
    type: Boolean,
    default: false,
  },
  zIndex: {
    type: Number,
    default: 50,
  },
  hideOverlay: {
    type: Boolean,
    default: false,
  },
  manageBodyScroll: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits<{
  close: []
  'update:modelValue': [value: boolean]
}>()

const close = () => {
  emit('update:modelValue', false)
  emit('close')
}

const contentRef = ref<HTMLElement | null>(null)
const hasScroll = ref(false)
let resizeObserver: ResizeObserver | null = null

const checkScroll = () => {
  if (contentRef.value) {
    hasScroll.value =
      contentRef.value.scrollHeight > contentRef.value.clientHeight
  }
}

watch(
  () => props.modelValue,
  async (isOpen) => {
    if (isOpen) {
      await nextTick()
      checkScroll()
    }
  },
)

onMounted(() => {
  if (contentRef.value) {
    resizeObserver = new ResizeObserver(checkScroll)
    resizeObserver.observe(contentRef.value)
  }
})

onUnmounted(() => {
  resizeObserver?.disconnect()
  document.body.style.overflow = ''
  document.body.style.paddingRight = ''
})

watch(
  () => props.modelValue,
  (isOpen) => {
    if (!props.manageBodyScroll) return

    if (isOpen) {
      const scrollbarWidth =
        window.innerWidth - document.documentElement.clientWidth

      document.body.style.overflow = 'hidden'
      if (scrollbarWidth > 0) {
        document.body.style.paddingRight = `${scrollbarWidth}px`
      }
    } else {
      document.body.style.overflow = ''
      document.body.style.paddingRight = ''
    }
  },
  { immediate: true },
)
</script>
