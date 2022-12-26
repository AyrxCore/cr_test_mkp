<template>
  <button
    class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium"
    :type="props.type"
    :class="{
      'button-no-click': isLoading,
      [btnColor]: true,
      [btnTextColor]: true,
      [rounded]: true,
    }"
    @click="onClick"
  >
    <LoaderSharedComponent v-if="isLoading" class="mr-2" />
    <slot name="default" />
  </button>
</template>

<script lang="ts" setup>
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { ButtonHTMLAttributes, PropType } from 'vue'

const props = defineProps({
  type: {
    required: false,
    type: String as PropType<ButtonHTMLAttributes['type']>,
    default: 'button',
  },
  isLoading: {
    required: false,
    type: Boolean,
    default: false,
  },
  rounded: {
    required: false,
    type: String,
    default: 'rounded-lg',
  },
  btnColor: {
    required: false,
    type: String,
    default: 'bg-purple-600',
  },
  btnTextColor: {
    required: false,
    type: String,
    default: 'text-white',
  },
})

const emit = defineEmits(['click'])

const onClick = ($event: PointerEvent): void => {
  if (props.isLoading) return
  emit('click', $event)
}
</script>
<style lang="scss" scoped>
.default-button {
  height: 32px;
  font-family: CoText, sans-serif;
  font-style: normal;
  font-weight: 700;
  font-size: 14px;
  line-height: 16px;
  text-align: center;
}
</style>
