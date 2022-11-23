<template>
  <div id="search-autocomplete" ref="wrapper" class="flex w-full">
    <input
      v-model="query"
      class="w-full rounded-l-lg border-gray-300 px-5 pl-12 text-gray-500 text-cotext"
      name="search"
      :placeholder="props.placeholder"
      type="search"
    />
    <button
      class="flex items-center rounded-r-lg px-3 py-1 text-white hover:opacity-75 text-cotext"
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
