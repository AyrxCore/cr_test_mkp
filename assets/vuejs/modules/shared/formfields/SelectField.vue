<template>
  <select
    v-model="internalValue"
    class="block w-full rounded-lg border-none p-2.5 text-sm focus:border-primary focus:ring-primary"
    :class="props.classes"
    :required="props.required"
    :disabled="props.disabled"
    @change="onChange($event)"
  >
    <option value="">{{ props.placeholder }}</option>
    <option
      v-for="(option, id) in props.options"
      :key="id"
      :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>
<script lang="ts" setup>
import { PropType, ref, watch } from 'vue'
import { SelectOption } from '@/vuejs/types/Select'

const internalValue = ref<string>('')

const props = defineProps({
  modelValue: {
    required: true,
    type: [String, Number],
  },
  placeholder: {
    required: false,
    type: String,
    default: 'Sélectionner une valeur',
  },
  classes: {
    required: false,
    type: String,
    default: '',
  },
  disabled: {
    required: false,
    type: Boolean,
    default: false,
  },
  options: {
    required: true,
    type: Object as PropType<SelectOption[]>,
  },
  required: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (eventName: 'change', value: string): void
}>()

watch(
  () => props.modelValue as string,
  (value: string) => {
    internalValue.value = value
  },
  { immediate: true },
)

const onChange = (event: Event): void => {
  emit('change', (event.target as HTMLInputElement).value)
  emit('update:modelValue', internalValue.value)
}
</script>
