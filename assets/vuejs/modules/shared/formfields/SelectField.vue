<template>
  <select
      v-model="internalValue"
      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm
        rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
      :class="props.classes"
      :disabled="props.disabled"
      @change="onChange($event); updateModel()"
  >
    <option value="">{{props.placeholder }}</option>
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
import {ref, watch, PropType} from 'vue'
import {SelectOption} from "@/vuejs/types/Select";

const internalValue = ref<string>('')

const props = defineProps({
  modelValue: {
    required: true,
    type: String,
  },
  placeholder: {
    required: false,
    type: String,
    default: 'Sélectionner une valeur pour filtrer la liste'
  },
  classes: {
    required: false,
    type: String,
    default: '',
  },
  disabled: {
    required: false,
    type: Boolean,
    default: false
  },
  options: {
    required: true,
    type: Object as PropType<SelectOption[]>
  },
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void,
  (eventName: 'change',value: string): void
}>()

const updateModel = () => {
  emit('update:modelValue', internalValue.value)
}

watch(
    () => props.modelValue as string,
    (value: string) => {
      internalValue.value = value
    },
    { immediate: true },
)

const onChange = (event: Event): void => {
  emit('change', (event.target as HTMLInputElement).value)
}
</script>
