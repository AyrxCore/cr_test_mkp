<template>
  <button
    class="contactez-nous mr-2 mb-2 flex items-center rounded-md bg-purple-600 px-5 py-2.5 text-sm font-medium text-white"
    :type="props.type"
    :class="{ 'button-no-click': isLoading }"
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
})

const emit = defineEmits(['click'])

const onClick = ($event: PointerEvent): void => {
  if (props.isLoading) return
  emit('click', $event)
}
</script>
