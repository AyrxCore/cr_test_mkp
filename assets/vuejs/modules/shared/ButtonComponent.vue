<template>
  <button
    :class="{ 'button-no-click': isLoading }"
    class="button"
    type="button"
    @click="onClick"
  >
    <LoaderSharedComponent v-if="isLoading" class="mr-2" />
    <slot v-else name="default" />
  </button>
</template>

<script lang="ts" setup>
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const props = defineProps({
  isLoading: {
    required: false,
    type: Boolean,
    default: false,
  },
  rounded: {
    required: false,
    type: String,
    default: 'rounded-md',
  },
})

const emit = defineEmits(['click'])

const onClick = ($event: PointerEvent): void => {
  if (props.isLoading) return
  emit('click', $event)
}
</script>

<style lang="postcss">
.button {
  @apply flex h-12 flex-nowrap items-center justify-center overflow-hidden whitespace-nowrap rounded-full px-8 py-4 text-sm text-white;
  &:hover:not(:disabled):not(:focus) {
    @apply bg-secondary bg-none shadow-[0_0_20px_0] shadow-secondary;
  }
  &:focus:not(:disabled) {
    @apply bg-primary bg-none;
  }
  &:disabled {
    @apply cursor-not-allowed opacity-50;
  }
  &-secondary {
    @apply bg-secondary;
  }
  &-gradient {
    @apply bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2;
  }
  &-white {
    @apply bg-white text-primary;
    &:hover:not(:disabled):not(:focus) {
      @apply bg-secondary text-white;
      svg,
      path {
        @apply stroke-white;
      }
    }
    svg,
    path {
      @apply stroke-primary;
    }
  }

  svg {
    &:last-of-type {
      @apply mr-1.5;
    }
    &:not(:last-of-type) {
      @apply mr-1;
    }
  }
}
</style>
