<template>
  <template v-if="props.pattern === ''">
    <input
        v-model="internalValue"
        :type="props.type"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm
        rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        :class="props.classes"
        :disabled="props.disabled"
        :readonly="props.readonly"
        :required="props.required"
        :placeholder="props.placeholder"
        @change="onChange($event); updateModel()"
    />
  </template>
  <template v-else>
    <input
        v-model="internalValue"
        :type="props.type"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm
        rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        :class="props.classes"
        :disabled="props.disabled"
        :readonly="props.readonly"
        :required="props.required"
        :pattern="props.pattern"
        :placeholder="props.placeholder"
        @change="onChange($event); updateModel()"
    />
  </template>
</template>

<script lang="ts" setup>
import {ref, watch} from 'vue'

const internalValue = ref<string>('')

const props = defineProps({
  modelValue: {
    required: true,
    type: String,
  },
  placeholder: {
    required: false,
    type: String,
    default: '',
  },
  type: {
    required: false,
    type: String,
    default: 'text',
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
  readonly: {
    required: false,
    type: Boolean,
    default: false
  },
  required: {
    required: false,
    type: Boolean,
    default: false
  },
  pattern: {
    required: false,
    type: String,
    default: ''
  }
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
