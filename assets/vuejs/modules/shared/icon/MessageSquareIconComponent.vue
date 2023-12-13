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
    <path
      d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
    ></path>
  </svg>
</template>

<script lang="ts" setup>
defineProps({
  fillColor: {
    type: String,
    default: 'none',
  },
  strokeColor: {
    type: String,
    default: '#9553FF',
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
