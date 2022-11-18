<template>
  <div id="search-autocomplete" ref="wrapper" class="flex w-full">
    <input
      v-model="query"
      class="rounded-l-lg border-gray-300 px-5 pl-12 text-gray-500 w-full"
      name="search"
      :placeholder="props.placeholder"
      type="search"
    />
    <button
      class="flex bg-purple-600 items-center px-3 py-1 rounded-r-lg text-white hover:opacity-75"
    >
      <i class="fa fa-search" />
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
})
</script>
