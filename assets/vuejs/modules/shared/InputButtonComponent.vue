<template>
  <div ref="wrapper" class="flex w-full">
    <input
      v-model="query"
      class="input !rounded-r-none !p-3 !text-sm md:!text-base md:!text-lg lg:!p-8"
      name="search"
      :placeholder="props.placeholder"
      :type="props.type"
    />
    <button
      class="flex items-center rounded-r-md px-3 py-1 text-white hover:opacity-75"
      :class="btnColor"
    >
      <slot />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

const query = ref<string>('')
const wrapper = ref<HTMLElement>()

const props = defineProps({
  type: {
    required: false,
    type: String,
    default: 'text',
  },
  placeholder: {
    required: false,
    type: String,
    default: 'Rechercher',
  },
  btnColor: {
    required: false,
    type: String,
    default: 'bg-purple-600',
  },
})
</script>
