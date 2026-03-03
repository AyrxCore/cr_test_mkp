<template>
  <div class="relative">
    <input
      v-model="internalValue"
      :type="showPassword ? 'text' : 'password'"
      :required="required"
      :disabled="disabled"
      :placeholder="placeholder"
      :class="classes"
      class="block w-full rounded-lg border-none p-2.5 pr-10 text-sm focus:border-primary focus:ring-primary"
      @focus="emit('focus')"
      @blur="emit('blur')"
      @change="onchange"
    />
    <button
      type="button"
      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
      tabindex="-1"
      @click="showPassword = !showPassword"
    >
      <EyeSlashIcon v-if="showPassword" />
      <EyeIcon v-else />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'
import EyeIcon from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import EyeSlashIcon from '@/vuejs/modules/shared/icon/EyeSlashIconComponent.vue'

const internalValue = ref<string>('')
const showPassword = ref<boolean>(false)

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
  required: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'change', value: string): void
  (e: 'focus'): void
  (e: 'blur'): void
}>()

watch(
  () => props.modelValue,
  (value) => { internalValue.value = value },
  { immediate: true },
)

const onchange = (event: Event) => {
  const val = (event.target as HTMLInputElement).value
  emit('change', val)
}

watch(internalValue, (value) => {
  emit('update:modelValue', value)
})
</script>
