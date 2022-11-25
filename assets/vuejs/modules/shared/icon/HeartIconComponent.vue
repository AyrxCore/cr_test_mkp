<template>
  <svg xmlns="http://www.w3.org/2000/svg"
       :width="sizeWidth"
       :height="sizeHeight"
       viewBox="0 0 24 24"
       :fill="fillColor"
       :stroke="strokeColor"
       stroke-width="2"
       stroke-linecap="round"
       stroke-linejoin="round">
    <path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.77.78-.77-.78a5.4 5.4 0 0 0-7.65 0C1.46 6.7 1.33 10.28 4 13l8 8 8-8c2.67-2.72 2.54-6.3.42-8.42z"></path>
  </svg>
</template>

<script lang="ts" setup>
const props = defineProps({
  fillColor: {
    type: String,
    default: 'none'
  },
  strokeColor: {
    type: String,
    default: '#FFF'
  },
  sizeHeight: {
    type: Number,
    default: 24
  },
  sizeWidth: {
    type: Number,
    default: 24
  }
})
</script>
