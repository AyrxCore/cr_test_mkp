<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :width="sizeWidth"
    :height="sizeHeight"
    viewBox="0 0 24 24"
    :fill="fillColor"
    :stroke="strokeColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
  >
    <polyline points="9 18 15 12 9 6"></polyline>
  </svg>
</template>

<script lang="ts" setup>
const props = defineProps({
  fillColor: {
    type: String,
    default: 'none',
  },
  strokeColor: {
    type: String,
    default: '#FFFFFF',
  },
  sizeWidth: {
    type: Number,
    default: 24,
  },
  sizeHeight: {
    type: Number,
    default: 24,
  },
})
</script>
