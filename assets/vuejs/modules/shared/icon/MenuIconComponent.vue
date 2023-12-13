<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :width="sizeWidth"
    :height="sizeHeight"
    :fill="fillColor"
    :stroke="strokeColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
  >
    <line x1="4" y1="12" x2="20" y2="12"></line>
    <line x1="4" y1="6" x2="20" y2="6"></line>
    <line x1="4" y1="18" x2="20" y2="18"></line>
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
    default: '#9866ff',
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
