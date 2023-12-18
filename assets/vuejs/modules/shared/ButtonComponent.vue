<template>
  <button
    :class="{ 'pointer-events-none': isLoading }"
    class="button"
    :type="props.type"
    @click="onClick"
  >
    <LoaderSharedComponent v-if="isLoading" />
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
  type: {
    required: false,
    type: String,
    default: 'submit',
  },
})

const emit = defineEmits(['click'])

const onClick = ($event: PointerEvent): void => {
  if (props.isLoading) return
  emit('click', $event)
}
</script>

<style lang="postcss">
.element {
  box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
}
.button {
  @apply inline-flex h-12 flex-nowrap items-center justify-center overflow-hidden whitespace-nowrap rounded-full px-8 py-4 text-sm text-white;
  &:hover:not(:disabled):not(:focus) {
    @apply border-secondary bg-secondary bg-none shadow-[0_0_20px_0] shadow-secondary;
  }
  &:focus:not(:disabled) {
    @apply bg-primary bg-none;
  }
  &:disabled {
    @apply cursor-not-allowed opacity-50;
  }
  &-primary {
    @apply bg-primary text-white;
    &:hover:not(:disabled):not(:focus) {
      @apply scale-105 bg-primary shadow-inner-lighter;
    }
  }
  &-secondary {
    @apply bg-secondary;
    svg,
    path {
      @apply stroke-white;
    }
  }
  &-secondary-definitive {
    @apply border-2 border-primary bg-white text-primary shadow-none;
    &:hover:not(:disabled):not(:focus) {
      @apply scale-105 border-primary bg-white shadow-inner-darker;
    }
  }
  &-secondary-outline {
    @apply border border-secondary bg-transparent text-secondary;
    &:focus:not(:disabled) {
      @apply border-primary text-white;
    }
    &:hover:not(:disabled):not(:focus) {
      @apply text-white;
    }
  }
  &-gradient {
    @apply bg-gradient-to-r from-secondary via-gradient-1 to-gradient-2 box-decoration-clone;
    svg,
    path {
      @apply stroke-white;
    }
  }

  &-white {
    @apply bg-white;

    &:hover:not(:disabled):not(:focus) {
      @apply bg-secondary text-white;

      svg,
      path {
        @apply stroke-white;
      }
    }

    &-primary {
      @apply bg-white text-primary;

      &:focus:not(:disabled) {
        @apply bg-white bg-none !text-primary;
      }

      svg,
      path {
        @apply stroke-primary;
      }
    }

    &-secondary {
      @apply border border-secondary text-secondary;

      &:focus:not(:disabled) {
        @apply border-none bg-none text-white;
      }

      svg,
      path {
        @apply stroke-secondary;
      }
    }
  }

  svg {
    &:last-of-type:not(.loader) {
      @apply mr-1.5;
    }

    &:not(:last-of-type) {
      @apply mr-1;
    }
  }
}
</style>
