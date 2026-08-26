<template>
  <button
    :class="{ 'pointer-events-none': isLoading }"
    :disabled="disabled"
    :type="props.type"
    class="button"
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
  disabled: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['click'])

const onClick = ($event: PointerEvent): void => {
  if (props.isLoading) return
  emit('click', $event)
}
</script>

<style>
@reference '@/style/main.css';

.element {
  box-shadow: inset 0 0 100px 100px rgba(255, 255, 255, 0.15);
}

.button {
  @apply inline-flex h-12 flex-nowrap items-center justify-center overflow-hidden whitespace-nowrap rounded-full px-8 py-4 text-sm text-white;

  &:disabled {
    @apply cursor-not-allowed opacity-50;
  }

  &:hover:not(:disabled):not(:focus) {
    @apply scale-105;
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

.button-primary {
  @apply bg-primary;

  &:hover:not(:disabled):not(:focus) {
    @apply shadow-inner-lighter;
  }
}

.button-primary-outline {
  @apply border-2 border-primary bg-white text-primary;

  &:hover:not(:disabled):not(:focus) {
    @apply shadow-inner-darker;
  }
}

.button-secondary {
  @apply border-2 border-secondary bg-secondary text-white;

  &:hover:not(:disabled):not(:focus) {
    @apply shadow-inner-darker;
  }
}

.button-primary-outline-white {
  @apply border-2 border-white bg-primary text-white;
}
</style>
